<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Event
{
    private array $params;

    public function __construct(array $params)
    {
        if (!array_key_exists('event_name', $params)) {
            throw new Exception('missing event name');
        }

        $name = $params['event_name'];

        $required = Helper::json_decode(file_get_contents(__DIR__ . '/json/required.json'), true, 5, JSON_THROW_ON_ERROR);

        if (!array_key_exists($name, $required)) {
            throw new Exception("unhandled event name - {$name}");
        }

        $required = $required[$name];

        $this->params = [];

        foreach ($required as $key) {
            if (array_key_exists($key, $params)) {
                $this->params[$key] = $params[$key];
            } else {
                $this->params[$key] = null;
            }
        }
    }

    public function valid() : self
    {
        foreach ($this->params as $key => $param) {
            if ($param === null) {
                throw new Exception("invalid param - {$key}");
            }
        }

        return $this;
    }

    public function params() : array
    {
        return $this->params;
    }
}
