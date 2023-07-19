<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Gtag
{
    private readonly array $params;

    public function __construct(array $params)
    {
        $this->params = array_merge([
            'protocol_version' => 2,
            'gtm' => '45je37h0',
            'random_p' => rand(1, 999999999),
            'ngs_unknown' => 1,
            'event_number' => 1,
            'session_id' => time() - 10,
            'session_number' => 1,
            'session_engaged' => true,
            'external_event' => true,
        ], $params);
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
