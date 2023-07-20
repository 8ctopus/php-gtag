<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Payload
{
    public function analyze(string $source) : string
    {
        $lines = explode("\n", trim($source));

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        // switch keys and values
        $names = array_flip($names);

        //echo json_encode($conversion, JSON_PRETTY_PRINT); die;

        // convert payload to array
        $payload = [];

        foreach ($lines as $line) {
            if (!str_contains($line, ':')) {
                throw new Exception("invalid line - {$line}");
            }

            [$name, $value] = explode(':', $line, 2);

            $value = trim($value);

            if (isset($names[$name])) {
                $payload[$names[$name]] = $value;
            } else {
                $payload[$name] = $value;
            }
        }

        // set array values type
        $types = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-types.json'), true, 5, JSON_THROW_ON_ERROR);

        foreach ($payload as $key => $value) {
            if (array_key_exists($key, $types)) {
                settype($payload[$key], $types[$key]);
            }
        }

        // sort payload
        $order = Helper::json_decode(file_get_contents(__DIR__ . '/json/payload-sort.json'), true, 5, JSON_THROW_ON_ERROR);

        uksort($payload, function($key1, $key2) use ($order) : int {
            $index1 = array_search($key1, $order);
            $index2 = array_search($key2, $order);

            return ($index1 < $index2) ? -1 : +1;
        });

        // translated array
        $converted = '';

        foreach ($payload as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'integer':
                case 'double':
                case 'string':
                    break;

                case 'boolean':
                    $value = $value ? 'true' : 'false';
                    break;

                default:
                    throw new Exception("unhandled type - {$type}");
            }

            $converted .= "{$key}: {$value}\n";
        }

        return $converted;
    }
}
