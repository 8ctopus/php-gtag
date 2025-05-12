<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;
use RuntimeException;

class Helper
{
    /**
     * Read browser cookies
     *
     * @param string $trackingId
     *
     * @return array
     *
     * @throws Exception
     */
    public static function readCookies(string $trackingId) : array
    {
        $cookies = [];

        if (!$_COOKIE || !array_key_exists('_ga', $_COOKIE)) {
            throw new Exception('cookie _ga not found');
        }

        $cookies['_ga'] = $_COOKIE['_ga'];

        $trackingId = str_replace('G-', '', $trackingId);

        $cookie = "_ga_{$trackingId}";

        if (!array_key_exists($cookie, $_COOKIE)) {
            throw new Exception('cookie _ga_* not found');
        }

        $cookies[$cookie] = $_COOKIE[$cookie];

        return $cookies;
    }

    /**
     * Create ga cookies when they don't exist because of ad blocks
     *
     * @param string $trackingId
     *
     * @return array
     */
    public static function createCookies(string $trackingId) : array
    {
        $cookies = [];

        $cookies['_ga'] = static::createClientId();

        $trackingId = str_replace('G-', '', $trackingId);

        $cookie = "_ga_{$trackingId}";

        $cookies[$cookie] = static::createSessionId();

        return $cookies;
    }

    /**
     * Create user cookie value
     *
     * @return string
     *
     * @note See cookies.md for format. example GA1.2.1987826055.1739862817
     */
    public static function createClientId() : string
    {
        return 'GA1.2.' . static::randomInt() . '.' . time();
    }

    /**
     * Create session cookie value
     *
     * @return string
     *
     * @note See cookies.md for format. example GS2.1.s1747027167$o11$g1$t1747027167$j0$l0$h0
     */
    public static function createSessionId() : string
    {
        $time = time();

        return "GS2.1.s{$time}.\$o1.\$g0.\$t{$time}.\$j0.\$l0.\$h0";
    }

    /**
     * Create expired session cookie value
     *
     * @return string
     */
    public static function createExpiredSessionId() : string
    {
        $time = time() - 31 * 60;

        return "GS2.1.s{$time}.\$o1.\$g0.\$t{$time}.\$j0.\$l0.\$h0";
    }

    public static function analyze(string $source) : string
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

        // check version
        $version = (int) $payload['protocol_version'];

        if ($version !== 2) {
            if ($version === 1) {
                throw new Exception('universal analytics is not supported');
            }

            throw new Exception("unsupported protocol version - {$version}");
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

        uksort($payload, function ($key1, $key2) use ($order) : int {
            $index1 = array_search($key1, $order, true);
            $index2 = array_search($key2, $order, true);

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

    /**
     * Decode json with invalid syntax
     *
     * @param string $json
     * @param bool   $associative
     * @param int    $depth
     * @param int    $options
     *
     * @return ?array
     *
     * @note taken from https://github.com/etconsilium/php-json-fix
     */
    public static function json_decode(string $json, bool $associative = false, int $depth = 512, int $options = JSON_BIGINT_AS_STRING) : ?array
    {
        // http://php.net/manual/ru/function.json-decode.php#112735
        // comments
        $json = preg_replace('~(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)~', '', $json);

        // trailing commas
        $json = preg_replace('~,\s*([\]}])~mui', '$1', $json);

        // empty cells
        $json = preg_replace('~(.+?:)(\s*)?([\]},])~mui', '$1null$3', $json);
        // $json = preg_replace('~.+?({.+}).+~', '$1', $json);

        // codes   //  @TODO: add \x
        $json = str_replace(["\n", "\r", "\t", "\0"], '', $json);

        /**
         * @TODO кавычки
         * $json = str_replace("'", '"', $json);
         */
        $decode = json_decode($json, $associative, $depth, $options);

        //  \Zend...\Json
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $decode;
                break;

            case JSON_ERROR_DEPTH:
                throw new RuntimeException('maximum stack depth exceeded');
                break;

            case JSON_ERROR_CTRL_CHAR:
                throw new RuntimeException('unexpected control character found');
                break;

            case JSON_ERROR_SYNTAX:
                throw new RuntimeException('syntax error');
                break;

            default:
                throw new RuntimeException(json_last_error_msg());
                break;
        }

        return null;
    }

    protected static function randomInt() : int
    {
        return random_int(100000000, 9999999999);
    }
}
