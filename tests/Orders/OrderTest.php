<?php

namespace Tests\Orders;

use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use PayPal\Checkout\Exceptions\InvalidOrderException;
use PayPal\Checkout\Exceptions\InvalidOrderIntentException;
use PayPal\Checkout\Orders\AmountBreakdown;
use PayPal\Checkout\Orders\ApplicationContext;
use PayPal\Checkout\Orders\Item;
use PayPal\Checkout\Orders\Order;
use PayPal\Checkout\Orders\PurchaseUnit;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * @test
     */
    public function canNotCreateOrderWithInvalidIntent()
    {
        $this->expectException(InvalidOrderIntentException::class);
        $this->expectExceptionMessage(<<<'MESSAGE'
Order intent provided is not supported. Please refer to https://developer.paypal.com/docs/api/orders/v2/#orders_create.
MESSAGE
        );

        new Order('Invalid Intent');
    }

    /**
     * @test
     */
    public function canNotSetInvalidIntentOnOrder()
    {
        $this->expectException(InvalidOrderIntentException::class);
        $this->expectExceptionMessage(<<<'MESSAGE'
Order intent provided is not supported. Please refer to https://developer.paypal.com/docs/api/orders/v2/#orders_create.
MESSAGE
        );

        $order = new Order();
        $order->setIntent('invalid intent');
    }

    /**
     * @test
     */
    public function canInitializeOrder()
    {
        // Act
        $order = new Order();

        // Assert
        $this->assertEquals('CAPTURE', $order->getIntent());
        $this->assertEquals([
            'locale' => 'en-US',
            'shipping_preference' => 'NO_SHIPPING',
            'landing_page' => 'NO_PREFERENCE',
            'user_action' => 'CONTINUE',
        ], $order->getApplicationContext()->toArray());

        // Act
        $order->setIntent('AUTHORIZE');

        // Assert
        $this->assertEquals('AUTHORIZE', $order->getIntent());
        $this->assertEmpty($order->getPurchaseUnits());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canAddPurchaseUnitToOrder()
    {
        // Arrange
        $amount = AmountBreakdown::of('200.00', 'EUR');
        $purchase_unit = new PurchaseUnit($amount);
        $order = new Order();

        // Act
        $order->addPurchaseUnit($purchase_unit);

        // Assert
        $this->assertCount(1, $order->getPurchaseUnits());
    }

    /**
     * @throws UnknownCurrencyException
     * @test
     */
    public function canNotAddMultiplePurchaseUnitToOrder()
    {
        // Arrange
        $this->expectException(InvalidOrderException::class);
        $this->expectExceptionMessage('At present only 1 purchase_unit is supported.');

        $amount = AmountBreakdown::of('200.00', 'EUR');
        $purchase_unit = new PurchaseUnit($amount);
        $order = new Order();

        // Act
        $order->addPurchaseUnit($purchase_unit);
        $order->addPurchaseUnit($purchase_unit);
    }

    /**
     * @test
     */
    public function assertsOrderHasAtLeastOnePurchaseUnit()
    {
        // Expect
        $this->expectException(InvalidOrderException::class);
        $this->expectExceptionMessage('Paypal orders must have 1 purchase_unit at least.');

        // Act
        (new Order())->toArray();
    }


    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function canCastToArray()
    {
        // Arrange
        $amount = AmountBreakdown::of('250.00');
        $amount->setItemTotal(Money::of('300', 'USD'));
        $amount->setDiscount(Money::of('50', 'USD'));
        $purchase_unit = new PurchaseUnit($amount);

        $purchase_unit->addItem(Item::make('Item 1', '100.00'));
        $purchase_unit->addItem(Item::make('Item 2', '100.00', 'USD', 2));

        $application_context = new ApplicationContext('Paypal Inc', 'en');
        $application_context->setUserAction('PAY_NOW');
        $application_context->setReturnUrl('https://site.com/payment/return');
        $application_context->setCancelUrl('https://site.com/payment/cancel');

        // Act
        $order = new Order('CAPTURE');
        $order->addPurchaseUnit($purchase_unit);
        $order->setApplicationContext($application_context);

        // Assert
        $expected = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '250.00',
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'USD',
                                'value' => '300.00',
                            ],
                            'discount' => [
                                'currency_code' => 'USD',
                                'value' => '50.00',
                            ],
                        ],
                    ],
                    'items' => [
                        [
                            'name' => 'Item 1',
                            'unit_amount' => [
                                'currency_code' => 'USD',
                                'value' => '100.00',
                            ],
                            'quantity' => 1,
                            'description' => '',
                            'category' => 'DIGITAL_GOODS',
                        ],
                        [
                            'name' => 'Item 2',
                            'unit_amount' => [
                                'currency_code' => 'USD',
                                'value' => '100.00',
                            ],
                            'quantity' => 2,
                            'description' => '',
                            'category' => 'DIGITAL_GOODS',
                        ],
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => 'Paypal Inc',
                'locale' => 'en',
                'shipping_preference' => 'NO_SHIPPING',
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url' => 'https://site.com/payment/return',
                'cancel_url' => 'https://site.com/payment/cancel',
            ],
        ];
        $this->assertEquals($expected, $order->toArray());
    }

    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function canCastToJson()
    {
        // Arrange
        $amount = AmountBreakdown::of('250.00');
        $amount->setItemTotal(Money::of('300', 'USD'));
        $amount->setDiscount(Money::of('50', 'USD'));
        $purchase_unit = new PurchaseUnit($amount);

        $purchase_unit->addItem(Item::make('Item 1', '100.00'));
        $purchase_unit->addItem(Item::make('Item 2', '100.00', 'USD', 2));

        $application_context = new ApplicationContext('Paypal Inc', 'en');
        $application_context->setUserAction('PAY_NOW');
        $application_context->setReturnUrl('https://site.com/payment/return');
        $application_context->setCancelUrl('https://site.com/payment/cancel');

        // Act
        $order = new Order('CAPTURE');
        $order->addPurchaseUnit($purchase_unit);
        $order->setApplicationContext($application_context);

        // Assert
        $expected = json_encode([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '250.00',
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'USD',
                                'value' => '300.00',
                            ],
                            'discount' => [
                                'currency_code' => 'USD',
                                'value' => '50.00',
                            ],
                        ],
                    ],
                    'items' => [
                        [
                            'name' => 'Item 1',
                            'unit_amount' => [
                                'currency_code' => 'USD',
                                'value' => '100.00',
                            ],
                            'quantity' => 1,
                            'description' => '',
                            'category' => 'DIGITAL_GOODS',
                        ],
                        [
                            'name' => 'Item 2',
                            'unit_amount' => [
                                'currency_code' => 'USD',
                                'value' => '100.00',
                            ],
                            'quantity' => 2,
                            'description' => '',
                            'category' => 'DIGITAL_GOODS',
                        ],
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => 'Paypal Inc',
                'locale' => 'en',
                'shipping_preference' => 'NO_SHIPPING',
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url' => 'https://site.com/payment/return',
                'cancel_url' => 'https://site.com/payment/cancel',
            ],
        ]);
        $this->assertEquals($expected, $order->toJson());
    }
}
