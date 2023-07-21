<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

use Exception;

abstract class AbstractEvent
{
    protected array $params = [];
    protected array $required;

    public function name() : string
    {
        return $this->params['event_name'];
    }

    public function valid() : self
    {
        if (!array_key_exists('event_name', $this->params)) {
            throw new Exception('missing event name');
        }

        foreach ($this->required as $key) {
            if (!array_key_exists($key, $this->params)) {
                throw new Exception("missing required - {$key}");
            }
        }

        return $this;
    }

    public function encode(array $params) : array
    {
        $params = array_merge($params, $this->params);

        $params = $this->sort($params);

        // convert names to keys
        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        $payload = [];

        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            $payload[$names[$key]] = $value;
        }

        return $payload;
    }

    public function ini(array $params) : string
    {
        $params = array_merge($params, $this->params);

        $params = $this->sort($params);

        $names = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-names.json'), true, 5, JSON_THROW_ON_ERROR);

        $payload = '';

        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $names)) {
                throw new Exception("unknown key - {$key}");
            }

            if (gettype($value) === 'boolean') {
                $value = $value ? '1' : '0';
            }

            $payload .= "{$names[$key]}: {$value}\n";
        }

        return $payload;
    }

    public function setDocumentLocation(string $url) : self
    {
        $this->params['document_location'] = $url;
        return $this;
    }

    public function setDocumentReferrer(string $url) : self
    {
        $this->params['document_referrer'] = $url;
        return $this;
    }

    public function setDocumentTitle(string $title) : self
    {
        $this->params['document_title'] = $title;
        return $this;
    }

    /**
     * Set engagement time
     *
     * @param int $time
     *
     * @note The page_view, first_visit, and session_start events don't have a user_engagement_msec parameter
     * because there was no engagement time since the previous event in the session. https://support.google.com/analytics/answer/11109416
     */
    public function setEngagementTime(int $time) : self
    {
        $this->params['engagement_time'] = $time;
        return $this;
    }

    protected function setName(string $name) : self
    {
        $this->params['event_name'] = $name;
        return $this;
    }

    private function sort(array $params) : array
    {
        $order = Helper::json_decode(file_get_contents(__DIR__ . '/json/param-sort.json'), true, 5, JSON_THROW_ON_ERROR);

        uksort($params, function ($key1, $key2) use ($order) : int {
            $index1 = array_search($key1, $order, true);
            $index2 = array_search($key2, $order, true);

            return ($index1 < $index2) ? -1 : +1;
        });

        return $params;
    }
}
