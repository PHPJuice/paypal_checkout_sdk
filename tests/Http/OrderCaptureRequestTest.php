<?php

namespace Tests\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PayPal\Checkout\Http\OrderCaptureRequest;
use PHPUnit\Framework\TestCase;

class OrderCaptureRequestTest extends TestCase
{
    public function testHasCorrectUri()
    {
        $request = new OrderCaptureRequest('1KC5501443316171H');
        $this->assertEquals('/v2/checkout/orders/1KC5501443316171H/capture', $request->getUri());
    }

    public function testHasCorrectMethod()
    {
        $request = new OrderCaptureRequest('1KC5501443316171H');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testHasCorrectHeaders()
    {
        $request = new OrderCaptureRequest('1KC5501443316171H');
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('return=representation', $request->getHeaderLine('Prefer'));
    }

    public function testExecuteRequest()
    {
        $mockResponse = Utils::jsonEncode([
            'id' => '1KC5501443316171H',
        ]);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $mockResponse),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->send(new OrderCaptureRequest('1KC5501443316171H'));

        $this->assertEquals(200, $response->getStatusCode());

        $result = Utils::jsonDecode((string) $response->getBody());
        $this->assertEquals('1KC5501443316171H', $result->id);
    }
}
