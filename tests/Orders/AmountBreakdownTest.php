<?php

namespace Tests\Orders;

use Brick\Money\Currency;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use PayPal\Checkout\Orders\AmountBreakdown;
use PHPUnit\Framework\TestCase;

class AmountBreakdownTest extends TestCase
{
    /**
     * @throws UnknownCurrencyException
     */
    public function testToArray()
    {
        // Arrange
        $currency = Currency::of('USD');
        $expected = [
            'currency_code' => $currency->getCurrencyCode(),
            'value' => "100.00",
            'breakdown' => [
                'item_total' => [
                    'currency_code' => $currency->getCurrencyCode(),
                    'value' => "100.00",
                ],
            ],
        ];

        // Act
        $amount = new AmountBreakdown("100.00", $currency->getCurrencyCode());

        // Assert
        $this->assertEquals($expected, $amount->toArray());
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function testToArrayWithDiscount()
    {
        // Arrange
        $currency = Currency::of('USD');
        $expected = [
            'currency_code' => $currency->getCurrencyCode(),
            'value' => "100.00",
            'breakdown' => [
                'item_total' => [
                    'currency_code' => $currency->getCurrencyCode(),
                    'value' => "150.00",
                ],
                'discount' => [
                    'currency_code' => $currency->getCurrencyCode(),
                    'value' => "50.00",
                ],
            ],
        ];

        // Act
        $amount = new AmountBreakdown("100.00", $currency->getCurrencyCode());
        $amount->setItemTotal(Money::of("150", $currency));
        $amount->setDiscount(Money::of("50", $currency));

        // Assert
        $this->assertEquals($expected, $amount->toArray());
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function testToJson()
    {
        // Arrange
        $currency = Currency::of('USD');
        $expectedJson = json_encode([
            "currency_code" => $currency->getCurrencyCode(),
            "value" => "100.00",
            "breakdown" => [
                "item_total" => [
                    "currency_code" => $currency->getCurrencyCode(),
                    "value" => "100.00"
                ]
            ]
        ]);

        // Act
        $amount = new AmountBreakdown("100.00", $currency->getCurrencyCode());

        // Assert
        $this->assertJsonStringEqualsJsonString($expectedJson, $amount->toJson());
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function testToJsonWithDiscount()
    {
        // Arrange
        $currency = Currency::of('USD');
        $expectedJson = json_encode([
            "currency_code" => "USD",
            "value" => "100.00",
            "breakdown" => [
                "item_total" => [
                    "currency_code" => "USD",
                    "value" => "150.00"
                ],
                "discount" => [
                    "currency_code" => "USD",
                    "value" => "50.00"
                ]
            ]
        ]);

        // Act
        $amount = new AmountBreakdown("100.00", $currency->getCurrencyCode());
        $amount->setItemTotal(Money::of("150", $currency));
        $amount->setDiscount(Money::of("50", $currency));

        // Assert
        $this->assertEquals($expectedJson, $amount->toJson());
    }
}
