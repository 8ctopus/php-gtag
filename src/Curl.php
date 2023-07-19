<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

class Curl
{
    public function send() : void
    {
        $url = "https://www.google-analytics.com/g/collect";

        $random = strtolower(bin2hex(random_bytes(2)));

        $conversionTable = [
            'protocol_version' => 'v',

            'tracking_id' => 'tid',
            'gtm' => 'gtm',
            'random' => '_p',
            'client_id' => 'cid',

            'user_language' => 'ul',
            'screen_resolution' => 'sr',

            'unknown_1' => 'ngs',
            'event_number' => '_s',

            'session_id' => 'sid',
            'session_number' => 'sct',
            'session_engaged' => 'seg',

            'document_location' => 'dl',
            'document_referrer' => 'dp',
            'document_title' => 'dt',

            'event_name' => 'en',

            'first_visit' => '_fv',
            'new_to_site' => '_nsi',
            'session_start' => '_ss',
            'external_event' => '_ee',

            'engagement_time' => '_et',

            'user_agent_architecture' => 'uaa',
            'user_agent_bitness' => 'uab',
            'user_agent_full_version_list' => 'uafvl',
            'user_agent_model' => 'uam',
            'user_agent_mobile' => 'uamb',
            'user_agent_platform' => 'uap',
            'user_agent_platform_version' => 'uapv',
            'user_agent_wow64' => 'uaw',

            'debug' => 'ep.debug_mode',
        ];

        $payloadNewPageViewInSession = [
            "debug" => "true",

            "protocol_version" => 2,
            "gtm" => "45je37c0",
            "random" => rand(1, 999999999),
            "event_number" => 1,

            "tracking_id" => "G-8XQMZ2E6TH",
            "client_id" => "948747482.1689681163",

            "session_id" => "1689681163",
            //"first_visit" => true,
            //"new_to_site" => true,
            //"session_start" => true,
            "session_number" => 1,
            "session_engaged" => 1,
            "external_event" => true,

            "ngs" => 1,

            "event_name" => "page_view",

            "document_location" => "https://test.com/{$random}/",
            "document_title" => $random,
            "document_referrer" => "https://test.io/",

            "user_language" => "en-us",
            "screen_resolution" => "1920x1080",
            "user_agent_architecture" => "x86",
            "user_agent_bitness" => "64",
            "user_agent_full_version_list" => "Not.A Brand;8.0.0.0|Chromium;114.0.5735.201|Microsoft Edge;114.0.1823.82",
            "user_agent_model" => "",
            "user_agent_mobile" => false,
            "user_agent_platform" => "Windows",
            "user_agent_platform_version" => "10.0.0",
            "user_agent_wow64" => 0,
        ];

        $payload = $payloadNewPageViewInSession;

        $encodedPayload = [];

        foreach ($payload as $key => $value) {
            if (isset($conversionTable[$key])) {
                $encodedPayload[$conversionTable[$key]] = $value;
            } else {
                $encodedPayload[$key] = $value;
            }
        }

        //echo var_dump($newPayload); die;

        $session = curl_init();

        $url .= '?' . http_build_query($encodedPayload);

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
        random: {$random}
        response: {$response}

        OUTPUT;
    }
}
