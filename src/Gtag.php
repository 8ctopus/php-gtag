<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Gtag
{
    //private bool $firstEvent;
    private readonly string $url;

    private array $params;

    public function __construct(array $cookies, bool $debug)
    {
        $this->url = "https://www.google-analytics.com/g/collect";

        $params = $this->readCookies($cookies);

        $this->params = array_merge([
            'protocol_version' => 2,
            'gtm' => '45je37h0',
            'ngs_unknown' => 1,
            'event_number' => 0,
            //'session_number' => 1,
            'external_event' => true,
        ], $params);

        if ($debug) {
            $this->params['debug'] = true;
        }
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

        $params['session_id'] = $matches[1];
        $params['session_number'] = $matches[2];
        $params['session_engaged'] = $matches[3] === '1' ? true : false;
        //$params['unknown_timestamp'] = $matches[4];

        return $params;
    }

    public function send(Event $event) : self
    {
        $this->params['random_p'] = rand(1, 999999999);

        ++$this->params['event_number'];

        $required = Helper::json_decode(file_get_contents(__DIR__ . '/json/required.json'), true, 5, JSON_THROW_ON_ERROR);
        $required = $required['gtag'];

        foreach ($required as $key) {
            if (!array_key_exists($key, $this->params)) {
                throw new Exception("missing required parameter - {$key}");
            }
        }

        $encoded = $this->encode($event);

        $session = curl_init();

        $url = $this->url . '?' . http_build_query($encoded);

        //echo $url; die;

        curl_setopt_array($session, [
            \CURLOPT_URL => $url,
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HEADER => false,
            \CURLOPT_FRESH_CONNECT => false,

            \CURLOPT_CONNECTTIMEOUT => 5,
            \CURLOPT_TIMEOUT => 5,

            \CURLOPT_VERBOSE => false,

            // fail verbosely if the HTTP code returned is greater than or equal to 400
            \CURLOPT_FAILONERROR => true,
        ]);

        $response = curl_exec($session);

        if ($response === false) {
            throw new Exception('curl - ' . curl_error($session));
        }

        $status = curl_getinfo($session, CURLINFO_RESPONSE_CODE);

        curl_close($session);

        echo <<<OUTPUT
        status: {$status}
        response: {$response}

        OUTPUT;

        return $this;
    }

    public function encode(Event $event) : array
    {
        $payload = [];

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        $params = array_merge($this->params, $event->params());

        $params = $this->sort($params);

        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            $payload[$names[$key]] = $value;
        }

        return $payload;
    }

    public function ini(Event $event) : string
    {
        $payload = '';

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        $params = array_merge($this->params, $event->params());

        $params = $this->sort($params);

        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            $payload .= "{$names[$key]}: {$value}\n";
        }

        return $payload;
    }

    private function sort(array $params) : array
    {
        $order = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-sort.json'), true, 5, JSON_THROW_ON_ERROR);

        $sorted = [];

        foreach ($order as $key) {
            if (array_key_exists($key, $params)) {
                $sorted[$key] = $params[$key];
            }
        }

        return $sorted;
    }
}
