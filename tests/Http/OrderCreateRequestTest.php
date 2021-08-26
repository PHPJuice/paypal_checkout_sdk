<?php

namespace Tests\Http;

use Brick\Money\Exception\UnknownCurrencyException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PayPal\Checkout\Http\OrderCreateRequest;
use PayPal\Checkout\Orders\AmountBreakdown;
use PayPal\Checkout\Orders\ApplicationContext;
use PayPal\Checkout\Orders\Item;
use PayPal\Checkout\Orders\Order;
use PayPal\Checkout\Orders\PurchaseUnit;
use PHPUnit\Framework\TestCase;

class OrderCreateRequestTest extends TestCase
{
    /**
     * @test
     */
    public function hasCorrectUri()
    {
        $request = new OrderCreateRequest();
        $this->assertEquals('/v2/checkout/orders', $request->getUri());
    }

    /**
     * @test
     */
    public function hasCorrectMethod()
    {
        $request = new OrderCreateRequest();
        $this->assertEquals('POST', $request->getMethod());
    }

    /**
     * @test
     */
    public function hasCorrectHeaders()
    {
        $request = new OrderCreateRequest();
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('return=representation', $request->getHeaderLine('Prefer'));
    }

    /**
     * @test
     * @throws UnknownCurrencyException
     */
    public function hasCorrectBody()
    {
        // Arrange
        $amount = AmountBreakdown::of('100.00');
        $purchase_unit = new PurchaseUnit($amount);
        $purchase_unit->addItem(Item::make('Item 1', '100.00'));

        $application_context = new ApplicationContext('Paypal Inc', 'en');
        $application_context->setUserAction('PAY_NOW');
        $application_context->setReturnUrl('https://site.com/payment/return');
        $application_context->setCancelUrl('https://site.com/payment/cancel');

        $order = new Order('CAPTURE');
        $order->addPurchaseUnit($purchase_unit);
        $order->setApplicationContext($application_context);

        // Act
        $request = new OrderCreateRequest($order);

        // Assert
        $this->assertEquals((string) $order, (string) $request->getBody());
        $this->assertEquals($order->toArray(), Utils::jsonDecode($request->getBody(), true));
    }

    /**
     * @test
     * @throws GuzzleException
     * @throws UnknownCurrencyException
     */
    public function canExecuteRequest()
    {
        // Arrange
        $amount = AmountBreakdown::of('100.00');
        $purchase_unit = new PurchaseUnit($amount);
        $purchase_unit->addItem(Item::make('Item 1', '100.00'));

        $application_context = new ApplicationContext('Paypal Inc', 'en');
        $application_context->setUserAction('PAY_NOW');
        $application_context->setReturnUrl('https://site.com/payment/return');
        $application_context->setCancelUrl('https://site.com/payment/cancel');

        $order = new Order('CAPTURE');
        $order->addPurchaseUnit($purchase_unit);
        $order->setApplicationContext($application_context);

        $client = $this->setupMockClient();

        // Act
        $response = $client->send(new OrderCreateRequest($order));

        // Asserts
        $this->assertEquals(200, $response->getStatusCode());
        $result = Utils::jsonDecode((string) $response->getBody(), true);
        $this->assertEquals([
            'id' => '1KC5501443316171H',
            'intent' => 'CAPTURE',
            'status' => 'CREATED'
        ], $result);
    }

    private function setupMockClient(): Client
    {
        $mockResponse = Utils::jsonEncode([
            'id' => '1KC5501443316171H',
            'intent' => 'CAPTURE',
            'status' => 'CREATED',
        ]);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $mockResponse),
        ]);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }
}
