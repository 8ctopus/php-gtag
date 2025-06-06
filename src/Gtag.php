<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

//FIX ME use function donatj\UserAgent\parse_user_agent;

class Gtag
{
    protected readonly string $url;
    protected readonly int $sessionDuration;

    protected int $lastActivity;
    protected array $params;

    private array $required = [
        'protocol_version',
        'tracking_id',
        'gtm',
        'random_p',
        'client_id',
        //'user_language',
        //'screen_resolution',
        //'ngs_unknown',
        'event_number',
        'session_id',
        'session_number',
        'session_engaged',
        'external_event',
    ];

    /**
     * Constructor
     *
     * @param array $cookies
     * @param bool  $debug
     */
    public function __construct(array $cookies, bool $debug)
    {
        $this->url = 'https://www.google-analytics.com/g/collect';
        $this->sessionDuration = 30 * 60;

        $params = $this->readCookies($cookies);

        $this->params = array_merge([
            'protocol_version' => 2,
            'gtm' => '45je37j0',
            //'ngs_unknown' => 1,
            'external_event' => true,
        ], $params);

        $this->randomP();

        if ($debug) {
            $this->params['debug'] = 'true';
        }
    }

    /**
     * Send event
     *
     * @param AbstractEvent $event
     * @param bool          $safeMode
     *
     * @return self
     */
    public function send(AbstractEvent $event, bool $safeMode) : self
    {
        // check event is valid, throws internally
        $event->valid();

        $params = $this->prepareParams($event, $safeMode);

        $this->validateParams($params);

        if ($safeMode) {
            // show payload in chromium format
            $ini = $event->ini($params);
            //echo $ini . "\n";

            echo Helper::analyze($ini) . "\n";
        }

        // encode payload
        $encoded = $event->encode($params);

        // do not send engagement time if session start
        if (array_key_exists('_ss', $encoded)) {
            unset($encoded['_et']);
        }

        // we want %20 not +
        $url = $this->url . '?' . http_build_query($encoded, '', '&', PHP_QUERY_RFC3986);

        if ($safeMode) {
            echo "{$url}\n\n";

            // confirm send
            echo 'send event? ';

            if (trim(fgets(STDIN)) !== 'y') {
                exit;
            }

            echo "\n";
        }

        $this->curl($url);

        // update session last activity
        $this->lastActivity = time();

        return $this;
    }

    /**
     * Add parameters
     *
     * @param  array $params
     *
     * @return self
     */
    public function addParams(array $params) : self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Check if session is expired
     *
     * @return bool
     */
    public function isSessionExpired() : bool
    {
        return (time() - $this->lastActivity) >= $this->sessionDuration;
    }

    /**
     * Send curl request
     *
     * @param  string $url
     *
     * @return self
     */
    protected function curl(string $url) : self
    {
        // send request
        $session = curl_init();

        // FIX ME make same headers as browser
        curl_setopt_array($session, [
            \CURLOPT_URL => $url,
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HEADER => false,
            \CURLOPT_FRESH_CONNECT => false,
            \CURLOPT_HTTPHEADER => [
                'Content-Length: 0',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ],

            \CURLOPT_CONNECTTIMEOUT => 5,
            \CURLOPT_TIMEOUT => 5,

            \CURLOPT_VERBOSE => false,

            // fail verbosely if the HTTP code returned is greater than or equal to 400
            \CURLOPT_FAILONERROR => true,
        ]);

        $response = curl_exec($session);

        if ($response === false) {
            throw new Exception('curl exec - ' . curl_error($session));
        }

        $status = curl_getinfo($session, CURLINFO_RESPONSE_CODE);

        curl_close($session);

        if ($status !== 204) {
            throw new Exception("invalid status - {$status}");
        }

        if ($response !== '') {
            throw new Exception("invalid response - {$response}");
        }

        return $this;
    }

    /**
     * Create random
     *
     * @return self
     */
    protected function randomP() : self
    {
        $this->params['random_p'] = random_int(1, 999999999);
        $this->params['event_number'] = 0;
        return $this;
    }

    /**
     * Prepare parameters
     *
     * @param  AbstractEvent $event
     * @param  bool          $safeMode
     *
     * @return array
     */
    private function prepareParams(AbstractEvent $event, bool $safeMode) : array
    {
        $params = [];

        // default session expires after 30 minutes of inactivity
        if ($this->isSessionExpired()) {
            if ($safeMode) {
                // create new session
                echo "session expired, create new session...\n";
            }

            $this->params['session_id'] = time();
            $this->lastActivity = time();

            $params['session_start'] = true;
            ++$this->params['session_number'];
            $this->params['session_engaged'] = false;

            // new session requires a new random p
            $this->randomP();

            // FIX ME user agent required on session start
            //$this->addUserAgent();
        } else {
            $this->params['session_engaged'] = true;
        }

        // some events require a new random p (purchase does not)
        if (in_array($event->name(), ['page_view'], true)) {
            $this->randomP();
        }

        ++$this->params['event_number'];

        return array_merge($this->params, $params);
    }

    /**
     * Validate parameters
     *
     * @param  array $params
     *
     * @return self
     */
    private function validateParams(array $params) : self
    {
        foreach ($this->required as $key) {
            if (!array_key_exists($key, $params)) {
                throw new Exception("missing required parameter - {$key}");
            }
        }

        return $this;
    }

    /**
     * Read cookies
     *
     * @param  array $cookies
     *
     * @return array
     *
     * @throws Exception
     */
    private function readCookies(array $cookies) : array
    {
        if (count($cookies) !== 2) {
            throw new Exception('exactly 2 cookies expected');
        }

        if (!array_key_exists('_ga', $cookies)) {
            throw new Exception('_ga cookie missing');
        }

        $ga = $cookies['_ga'];

        // may not always be GA1.1 https://stackoverflow.com/a/16107194/10126479
        // sometimes cookie looks like this GA1.1.GA1.2.202830711.1689950339
        //                                  GA1.1.GA1.2.2497990.1693488014
        // new cookie format GA1.2.680519491.1740808099
        if (preg_match('/^GA1\.[12](?:\.GA1\.[12])?\.(\d{6,10}\.\d{10})$/', $ga, $matches) !== 1) {
            throw new Exception("_ga cookie invalid format - {$ga}");
        }

        $params = [];
        $params['client_id'] = $matches[1];

        unset($cookies['_ga']);

        $measurementId = key($cookies);

        if (!array_key_exists($measurementId, $cookies)) {
            throw new Exception("cookie missing - {$measurementId}");
        }

        $session = $cookies[$measurementId];

        $params['tracking_id'] = str_replace('_ga_', 'G-', $measurementId);

        // legacy GS1.1 format
        if (preg_match('/^GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d$/', $session, $matches) === 1) {
            $params['session_id'] = (int) $matches[1];
            $params['session_number'] = (int) $matches[2];
            $params['session_engaged'] = $matches[3] === '1';

            $this->lastActivity = (int) $matches[4];

            return $params;
        }

        // GS2.1 format
        if (preg_match('/^GS2\.1\.s(\d{10})\$o(\d{1,2})\$g(0|1)\$t(\d{10})\$j\d{1,2}\$l\d\$h\d$/', $session, $matches) !== 1) {
            throw new Exception("session cookie invalid format - {$session}");
        }

        $params['session_id'] = (int) $matches[1];
        $params['session_number'] = (int) $matches[2];
        $params['session_engaged'] = $matches[3] === '1';

        $this->lastActivity = (int) $matches[4];

        return $params;
    }

    /* NOT READY
    public static function addUserAgent(array $params) : array
    {
        //$userAgent = $_SERVER['HTTP_USER_AGENT'];
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36 Edg/112.0.1722.58';

        // or use sec header?
        $params["user_agent_full_version_list"] = $userAgent;

        $info = parse_user_agent($userAgent);

        if (preg_match('/(\(.*?); (.*?); (.*?)\)/', $userAgent, $matches) !== 1) {
            return $params;
        }

        $architecture = $matches[3];

        $params["user_agent_architecture"] = $architecture;
        $params["user_agent_bitness"] = $architecture === 'x64' ? 64 : 32;
        $params["user_agent_model"] = '';
        $params["user_agent_mobile"] = '';
        $params["user_agent_platform"] = $info['platform'];
        $params["user_agent_platform_version"] = '';
        $params["user_agent_wow64"] = '';

        return $params;
    }
    */
}
