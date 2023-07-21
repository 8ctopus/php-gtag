<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

class PurchaseEvent extends AbstractEvent
{
    protected array $required = [
        'document_location',
        'document_referrer',
        'document_title',

        'transaction_id',
        'transaction_value',
        'currency',
        'conversion',
        'product_1',
        'engagement_time',
    ];

    protected array $items;

    public function __construct()
    {
        $this->setName('purchase');
        $this->params['conversion'] = true;
    }

    public function setTransactionId(string $id) : self
    {
        $this->params['transaction_id'] = $id;
        return $this;
    }

    public function setTransactionValue(float $value) : self
    {
        $this->params['transaction_value'] = $value;
        return $this;
    }

    public function setCurrency(string $currency) : self
    {
        $this->params['currency'] = $currency;
        return $this;
    }

    public function addItem(string $name, int $quantity, float $price) : self
    {
        $this->items[] = [
            'name' => $name,
            'quantity' => $quantity,
            'price' => $price,
        ];

        $index = count($this->items);

        $this->params["product_{$index}"] = "nm{$name}~pr{$price}~qt{$quantity}";

        return $this;
    }
}
