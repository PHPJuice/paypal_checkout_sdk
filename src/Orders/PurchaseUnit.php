<?php

namespace PayPal\Checkout\Orders;

use PayPal\Checkout\Concerns\CastsToJson;
use PayPal\Checkout\Concerns\HasCollection;
use PayPal\Checkout\Contracts\Arrayable;
use PayPal\Checkout\Contracts\Jsonable;
use PayPal\Checkout\Exceptions\MultiCurrencyOrderException;

/**
 * https://developer.paypal.com/docs/api/orders/v2/#definition-purchase_unit.
 */
class PurchaseUnit implements Arrayable, Jsonable
{
    use CastsToJson;
    use HasCollection;

    /**
     * The total order Amount with an optional breakdown that provides details,
     * such as the total item Amount, total tax Amount, shipping, handling, insurance,
     * and discounts, if any.
     *
     * @var AmountBreakdown
     */
    protected AmountBreakdown $amount;

    /**
     * An array of items that the customer purchases from the merchant.
     *
     * @var Item[]
     */
    protected array $items = [];

    /**
     * Create a new collection.
     */
    public function __construct(AmountBreakdown $amount)
    {
        $this->amount = $amount;
    }

    /**
     *  push a new item into items array.
     * @param  Item[]  $items
     * @return PurchaseUnit
     * @throws MultiCurrencyOrderException
     */
    public function addItems(array $items): self
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    /**
     *  push a new item into items array.
     * @param  Item  $item
     * @return PurchaseUnit
     * @throws MultiCurrencyOrderException
     */
    public function addItem(Item $item): self
    {
        if ($item->getAmount()->getCurrencyCode() != $this->amount->getCurrencyCode()) {
            throw new MultiCurrencyOrderException();
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * return's purchase unit items.
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * return's the purchase unit amount breakdown.
     * @return AmountBreakdown
     */
    public function getAmount(): AmountBreakdown
    {
        return $this->amount;
    }

    /**
     * convert a purchase unit instance to array.
     * @return array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount->toArray(),
            'items' => array_map(
                fn(Item $item) => $item->toArray(),
                $this->items
            ),
        ];
    }
}
