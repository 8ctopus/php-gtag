<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

class PageviewEvent extends AbstractEvent
{
    protected array $required = [
        'document_location',
        'document_referrer',
        'document_title',
    ];

    public function __construct()
    {
        $this->setName('page_view');
    }
}
