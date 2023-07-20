<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

class PageviewEvent extends AbstractEvent
{
    protected array $required = [
        "document_location",
        "document_referrer",
        "document_title",
    ];

    public function __construct()
    {
        $this->setEventName('page_view');
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
}
