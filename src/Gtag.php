<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Gtag
{
    //private bool $firstEvent;
    private readonly string $url;

    private array $params;

    public function __construct(array $params)
    {
        //$this->firstEvent = false;
        $this->url = "https://www.google-analytics.com/g/collect";

        $this->params = array_merge([
            'protocol_version' => 2,
            'gtm' => '45je37h0',
            'ngs_unknown' => 1,
            'event_number' => 0,
            'session_number' => 1,
            'external_event' => true,
        ], $params);
    }

    public function send(Event $event) : self
    {
        $this->params['random_p'] = rand(1, 999999999);

        //if (!$this->firstEvent) {
        ++$this->params['event_number'];
        //$this->params['session_engaged'] = true;
        //} else {
        //    $this->firstEvent = false;
        //}

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
