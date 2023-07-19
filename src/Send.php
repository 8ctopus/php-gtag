<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

class Send
{
    private readonly string $url;

    public function __construct()
    {
        $this->url = "https://www.google-analytics.com/g/collect";
    }

    public function send(Event $event) : void
    {
        $session = curl_init();

        $url = $this->url . '?' . http_build_query($event->encode());

        echo $url; die;

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
    }
}
