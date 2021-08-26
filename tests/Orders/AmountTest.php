<?php

namespace Tests\Orders;

use Brick\Money\Exception\UnknownCurrencyException;
use PayPal\Checkout\Orders\Amount;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canInitializeAmount()
    {
        // Arrange
        $value = "100.00";
        $currency_code = "USD";

        // Act
        $amount = new Amount($value, $currency_code);

        // Assert
        $this->assertEquals($value, $amount->getValue());
        $this->assertEquals($currency_code, $amount->getCurrencyCode());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canInitializeAmountUsingOf()
    {
        // Arrange
        $value = "100.00";
        $currency_code = "USD";

        // Act
        $amount = Amount::of($value, $currency_code);

        // Assert
        $this->assertEquals($value, $amount->getValue());
        $this->assertEquals($currency_code, $amount->getCurrencyCode());
    }

    /**
     * @test
     */
    public function canCastToArray()
    {
        // Arrange
        $expected = [
            'value' => "100.00",
            'currency_code' => 'CAD',
        ];

        // Act
        $amount = new Amount("100.00", 'CAD');

        // Assert
        $this->assertEquals($expected, $amount->toArray());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canCastToArrayUsingOf()
    {
        // Arrange
        $expected = [
            'value' => "100.00",
            'currency_code' => 'CAD',
        ];

        // Act
        $amount = Amount::of("100.00", 'CAD');

        // Assert
        $this->assertEquals($expected, $amount->toArray());
    }

    /**
     * @test
     */
    public function canCastToJon()
    {
        $expectedJson = ' {
            "currency_code": "CAD",
            "value": "100.00"
        }';
        $amount = new Amount("100.00", 'CAD');
        $this->assertJsonStringEqualsJsonString($expectedJson, $amount->toJson());
    }

    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function canCastToJsonUsingOf()
    {
        $expectedJson = ' {
            "currency_code": "CAD",
            "value": "100.00"
        }';
        $amount = Amount::of("100.00", 'CAD');
        $this->assertJsonStringEqualsJsonString($expectedJson, $amount->toJson());
    }

    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function itThrowsUnknownCurrencyException()
    {
        $this->expectException(UnknownCurrencyException::class);

        Amount::of("100.00", 'R');
    }
}
