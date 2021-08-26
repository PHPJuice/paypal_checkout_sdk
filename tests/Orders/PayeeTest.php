<?php /** @noinspection SpellCheckingInspection */

namespace Tests\Orders;

use PayPal\Checkout\Orders\Payee;
use PHPUnit\Framework\TestCase;

class PayeeTest extends TestCase
{
    /**
     * @test
     */
    public function canMakePayee()
    {
        // Arrange
        $expected = [
            'email_address' => 'payee@paypal.com',
            'merchant_id' => 'YP568Y95AVSDY',
        ];

        // Act
        $payee = Payee::make('payee@paypal.com', 'YP568Y95AVSDY');

        // Assert
        $this->assertEquals($expected, $payee->toArray());
    }

    /**
     * @test
     */
    public function canCastToArray()
    {
        // Arrange
        $expected = [
            'email_address' => 'payee@paypal.com',
            'merchant_id' => 'YP568Y95AVSDY',
        ];

        // Act
        $payee = new Payee('payee@paypal.com', 'YP568Y95AVSDY');

        // Assert
        $this->assertEquals($expected, $payee->toArray());
    }

    /**
     * @test
     */
    public function canCastToJson()
    {
        // Arrange
        $expected = json_encode([
            'email_address' => 'payee@paypal.com',
            'merchant_id' => 'YP568Y95AVSDY',
        ]);

        // Act
        $payee = Payee::make('payee@paypal.com', 'YP568Y95AVSDY');

        // Assert
        $this->assertJsonStringEqualsJsonString($expected, $payee->toJson());
    }
}
