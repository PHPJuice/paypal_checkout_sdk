<?php

namespace Tests\Orders;

use Brick\Money\Exception\UnknownCurrencyException;
use PayPal\Checkout\Exceptions\MultiCurrencyOrderException;
use PayPal\Checkout\Orders\Amount;
use PayPal\Checkout\Orders\AmountBreakdown;
use PayPal\Checkout\Orders\Item;
use PayPal\Checkout\Orders\PurchaseUnit;
use PHPUnit\Framework\TestCase;

class PurchaseUnitTest extends TestCase
{
    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canInitializePurchaseUnit()
    {
        // Arrange
        $amount = AmountBreakdown::of('100.00', 'CAD');

        // Act
        $purchase_unit = new PurchaseUnit($amount);

        // Assert
        $this->assertEquals('CAD', $purchase_unit->getAmount()->getCurrencyCode());
        $this->assertEquals('100.00', $purchase_unit->getAmount()->getValue());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function itCreatesEmptyPurchaseUnit()
    {
        // Arrange
        $amount = AmountBreakdown::of('100.00', 'CAD');

        // Act
        $purchase_unit = new PurchaseUnit($amount);

        // Assert
        $this->assertCount(0, $purchase_unit->getItems());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canAddSingleItem()
    {
        // Arrange
        $amount = AmountBreakdown::of('100', 'CAD');
        $purchase_unit = new PurchaseUnit($amount);

        // Act
        $purchase_unit->addItem(Item::make('Item 1', '100.00', 'CAD', 2));

        // Assert
        $this->assertCount(1, $purchase_unit->getItems());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canAddMultipleItems()
    {
        // Arrange
        $amount = AmountBreakdown::of('100', 'CAD');
        $purchase_unit = new PurchaseUnit($amount);
        $items = array_map(function ($index) {
            return Item::make("Item $index", '100.00', 'CAD', $index);
        }, [1, 2, 3]);

        // Act
        $purchase_unit->addItems($items);

        // Assert
        $this->assertCount(3, $purchase_unit->getItems());
    }

    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function itThrowsMultiCurrencyException()
    {
        // Arrange
        $this->expectException(MultiCurrencyOrderException::class);

        $amount = AmountBreakdown::of('100', 'CAD');
        $purchase_unit = new PurchaseUnit($amount);

        // Act
        $item = Item::make('Item', '100', 'EUR', 2);

        $purchase_unit->addItem($item);
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canCastToArray()
    {
        // Arrange
        $amount = AmountBreakdown::of('300.00', 'CAD');
        $purchase_unit = new PurchaseUnit($amount);

        // Act
        $item_amount = Amount::of('100.00', 'CAD');
        $item1 = new Item('Item 1', $item_amount, 1);
        $item2 = new Item('Item 2', $item_amount, 2);
        $purchase_unit->addItem($item1)->addItem($item2);

        // Assert
        $expected = [
            'amount' => [
                'currency_code' => 'CAD',
                'value' => '300.00',
                'breakdown' => [
                    'item_total' => [
                        'currency_code' => 'CAD',
                        'value' => '300.00',
                    ],
                ],
            ],
            'items' => [
                [
                    'name' => 'Item 1',
                    'unit_amount' => [
                        'currency_code' => 'CAD',
                        'value' => '100.00',
                    ],
                    'quantity' => 1,
                    'description' => '',
                    'category' => 'DIGITAL_GOODS',
                ],
                [
                    'name' => 'Item 2',
                    'unit_amount' => [
                        'currency_code' => 'CAD',
                        'value' => '100.00',
                    ],
                    'quantity' => 2,
                    'description' => '',
                    'category' => 'DIGITAL_GOODS',
                ],
            ],
        ];
        $this->assertEquals($expected, $purchase_unit->toArray());
    }
}
