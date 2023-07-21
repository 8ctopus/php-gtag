<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use RuntimeException;

class Helper
{
    /**
     * Create a new GA4 client id
     *
     * @return string
     *
     * @note format is GA1.1.random(10).timestamp. See cookies.md for more info.
     * example GA1.1.1827526090.1689745728
     */
    public static function createClientId() : string
    {
        return 'GA1.1.' . static::randomInt() . '.' . time();
    }

    /**
     * Create a new GA4 session
     *
     * @return string
     *
     * @note format is GS1.1.session_id(timestamp).session_number.session_engaged.last_activity.?.?.? See cookies.md for more info.
     * example GS1.1.1689765380.3.1.1689766550.0.0.0
     */
    public static function createSessionId() : string
    {
        return 'GS1.1.' . time() . '.1.0.' . time() . '.0.0.0';
    }

    /**
     * Decode json with invalid syntax
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
        return random_int(1000000000, 9999999999);
    }
}
