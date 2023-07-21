<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

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
        'user_language',
        'screen_resolution',
        //'ngs_unknown',
        'event_number',
        'session_id',
        'session_number',
        'session_engaged',
        'external_event',
    ];

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

        // do not send engagement time if session start
        if (array_key_exists('session_start', $params)) {
            unset($params['engagement_time']);
        }

        // encode payload
        $encoded = $event->encode($params);

        // we want %20 not +
        $url = $this->url . '?' . http_build_query($encoded, '', null, PHP_QUERY_RFC3986);

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

    public function addParams(array $params) : self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

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

    protected function randomP() : self
    {
        $this->params['random_p'] = random_int(1, 999999999);
        $this->params['event_number'] = 0;
        return $this;
    }

    private function prepareParams(AbstractEvent $event, bool $safeMode) : array
    {
        $params = [];

        // default session expires after 30 minutes of inactivity
        if ((time() - $this->lastActivity) >= $this->sessionDuration) {
            // create new session
            echo "session expired, create new session...\n";

            $this->params['session_id'] = time();
            $this->lastActivity = time();

            $params['session_start'] = true;
            ++$this->params['session_number'];
            $this->params['session_engaged'] = false;

            // new session requires a new random p
            $this->randomP();
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

    private function validateParams(array $params) : self
    {
        foreach ($this->required as $key) {
            if (!array_key_exists($key, $params)) {
                throw new Exception("missing required parameter - {$key}");
            }
        }

        return $this;
    }

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
        if (preg_match('/^GA1\.1\.\d{10}\.\d{10}$/', $ga) !== 1) {
            throw new Exception('_ga cookie invalid format');
        }

        $params = [];

        $params['client_id'] = str_replace('GA1.1.', '', $ga);

        unset($cookies['_ga']);

        $trackingId = key($cookies);

        $session = $cookies[$trackingId];

        $params['tracking_id'] = str_replace('_ga_', 'G-', $trackingId);

        if (preg_match('/^GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d$/', $session, $matches) !== 1) {
            throw new Exception('session cookie invalid format');
        }

        $params['session_id'] = (int) $matches[1];
        $params['session_number'] = (int) $matches[2];
        $params['session_engaged'] = $matches[3] === '1' ? true : false;

        $this->lastActivity = (int) $matches[4];

        return $params;
    }
}
