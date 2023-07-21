<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Gtag
{
    private readonly string $url;
    private readonly int $sessionDuration;

    private int $lastActivity;
    private array $params;

    public function __construct(array $cookies, bool $debug)
    {
        $this->url = 'https://www.google-analytics.com/g/collect';
        $this->sessionDuration = 30 * 60;

        $params = $this->readCookies($cookies);

        $this->params = array_merge([
            'protocol_version' => 2,
            'gtm' => '45je37h0',
            'ngs_unknown' => 1,
            'external_event' => true,
            'random_p' => rand(1, 999999999),
            'event_number' => 0,
        ], $params);

        if ($debug) {
            $this->params['debug'] = 'true';
        }
    }

    public function send(AbstractEvent $event) : self
    {
        $params = [];

        // default session expires after 30 minutes of inactivity
        if ((time() - $this->lastActivity) >= $this->sessionDuration) {
            // create new session
            $this->params['session_id'] = time();
            $this->lastActivity = time();

            $params['session_start'] = true;
            ++$this->params['session_number'];
            $this->params['session_engaged'] = false;

            // new session requires a new random p
            $this->newRandomP();
        } else {
            $this->params['session_engaged'] = true;
        }

        // some events require a new random p (purchase does not)
        if (in_array($event->eventName(), ['page_view'], true)) {
            $this->newRandomP();
        }

        ++$this->params['event_number'];

        $params = array_merge($this->params, $params);

        $required = Helper::json_decode(file_get_contents(__DIR__ . '/json/required.json'), true, 5, JSON_THROW_ON_ERROR);
        $required = $required['gtag'];

        foreach ($required as $key) {
            if (!array_key_exists($key, $params)) {
                throw new Exception("missing required parameter - {$key}");
            }
        }

        // check event is valid, throws internally
        $event->valid();

        // show payload in chromium format
        echo $event->ini($params) . "\n";

        // encode payload
        $encoded = $event->encode($params);

        // we want %20 not +
        $url = $this->url . '?' . http_build_query($encoded, '', null, PHP_QUERY_RFC3986);
        echo "{$url}\n\n";

        // confirm send
        echo 'send event? ';

        if (trim(fgets(STDIN)) !== 'y') {
            exit;
        }

        echo "\n";

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

        echo <<<OUTPUT
        status: {$status}
        response: {$response}

        OUTPUT;

        if ($status !== 204) {
            throw new Exception("invalid status - {$status}");
        }

        if ($response !== '') {
            throw new Exception("invalid response - {$response}");
        }

        // update session last activity
        $this->lastActivity = time();

        return $this;
    }

    public function addParams(array $params) : self
    {
        $this->params = array_merge($this->params, $params);
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

        if (preg_match('/GA1\.1\.\d{10}\.\d{10}/', $ga) !== 1) {
            throw new Exception('_ga cookie invalid format');
        }

        $params = [];

        $params['client_id'] = str_replace('GA1.1.', '', $ga);

        unset($cookies['_ga']);

        $trackingId = key($cookies);

        $session = $cookies[$trackingId];

        $params['tracking_id'] = str_replace('_ga_', 'G-', $trackingId);

        if (preg_match('/GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d/', $session, $matches) !== 1) {
            throw new Exception('session cookie invalid format');
        }

        $params['session_id'] = (int) $matches[1];
        $params['session_number'] = (int) $matches[2];
        $params['session_engaged'] = $matches[3] === '1' ? true : false;

        $this->lastActivity = (int) $matches[4];

        return $params;
    }

    private function newRandomP() : self
    {
        $this->params['random_p'] = rand(1, 999999999);
        $this->params['event_number'] = 0;
        return $this;
    }
}
