<?php

namespace Tests\Orders;

use Brick\Money\Exception\UnknownCurrencyException;
use PayPal\Checkout\Exceptions\InvalidItemCategoryException;
use PayPal\Checkout\Orders\Amount;
use PayPal\Checkout\Orders\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canInitializeNewItem()
    {
        // Arrange
        $value = "100.00";
        $currency_code = "USD";
        $amount = new Amount($value, $currency_code);

        // Act
        $item = new Item('Item 1', $amount, 1);

        // Assert
        $this->assertEquals('Item 1', $item->getName());
        $this->assertEquals(1, $item->getQuantity());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canMakeItem()
    {
        // Arrange
        $value = "100.00";
        $currency_code = "USD";

        // Act
        $item = Item::make('Item 1', $value, $currency_code, 2);

        // Assert
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('Item 1', $item->getName());
        $this->assertEquals(2, $item->getQuantity());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canCastToArray()
    {
        // Arrange
        $expected = [
            'name' => 'Item 1',
            'unit_amount' => [
                'currency_code' => 'CAD',
                'value' => '100.00',
            ],
            'quantity' => 2,
            'description' => 'Item Description',
            'category' => 'DIGITAL_GOODS',
        ];

        // Act
        $item = Item::make('Item 1', "100.00", "CAD");
        $item->setDescription('Item Description')
            ->setQuantity(2);

        // Assert
        $this->assertEquals($expected, $item->toArray());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canCastToJson()
    {
        // Arrange
        $expected = json_encode([
            'name' => 'Item 1',
            'unit_amount' => [
                'currency_code' => 'CAD',
                'value' => '100.00',
            ],
            'quantity' => 2,
            'description' => 'Item Description',
            'category' => 'DIGITAL_GOODS',
        ]);

        // Act
        $item = Item::make('Item 1', "100.00", "CAD");
        $item->setDescription('Item Description')
            ->setQuantity(2);

        // Assert
        $this->assertJsonStringEqualsJsonString($expected, $item->toJson());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canNotAcceptInvalidItemCategory()
    {
        // Arrange
        $this->expectException(InvalidItemCategoryException::class);
        $this->expectExceptionMessage(<<<'MSG'
Item category is not supported. Please refer to https://developer.paypal.com/docs/api/orders/v2/#definition-item.
MSG
        );

        // Act
        $item = Item::make('Item', '100.00', 'CAD', 2);
        $item->setCategory('invalid category');
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canReturnItemSku()
    {
        // Arrange
        $item = Item::make('Item', '100.00', 'CAD', 2);

        // Act
        $item->setSku('123456789');

        // Assert
        $this->assertEquals('123456789', $item->getSku());
    }
}
