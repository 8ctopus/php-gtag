<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Event
{
    private readonly array $required;
    private array $event;
    private bool $debug;

    public function __construct(string $type, array $known, bool $debug)
    {
        $this->debug = $debug;

        $this->required = Helper::json_decode(file_get_contents(__DIR__ . '/json/required.json'), true, 5, JSON_THROW_ON_ERROR);

        if (!array_key_exists($type, $this->required)) {
            throw new Exception("unhandled event type - {$type}");
        }

        $required = $this->required[$type];
        $known['event_name'] = $type;

        $this->event = [];

        foreach ($required as $key) {
            if (array_key_exists($key, $known)) {
                $this->event[$key] = $known[$key];
            } else {
                $this->event[$key] = null;
            }
        }

        if ($this->debug) {
            // FIX ME debug is not in the right place
            $this->event['debug'] = true;
        }
    }

    public function encode() : array
    {
        $payload = [];

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        foreach ($this->event as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            $payload[$names[$key]] = $value;
        }

        return $payload;
    }

    public function ini() : string
    {
        $payload = '';

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        foreach ($this->event as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            $payload .= "{$names[$key]}: {$value}\n";
        }

        return $payload;
    }
}
