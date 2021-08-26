# PayPal Checkout SDK

![PHP Composer](https://github.com/phpjuice/paypal-checkout-sdk/workflows/PHP%20Composer/badge.svg?branch=master)
[![Build Status](https://travis-ci.com/phpjuice/paypal-checkout-sdk.svg?branch=master)](https://travis-ci.com/phpjuice/paypal-checkout-sdk)
[![Latest Stable Version](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/v)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk) 
[![Total Downloads](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/downloads)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
[![License](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/license)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)

This Package is a PHP SDK wrapper around version 2 of the PayPal rest API. It provides a simple, fluent API to create
and capture orders with both sandbox and production environments supported.

## Installation

PayPal Checkout SDK Package requires PHP 7.4 or higher.

> **INFO:** If you are using an older version of php this package may not function correctly.

The supported way of installing PayPal Checkout SDK package is via Composer.

```bash
composer require phpjuice/paypal-checkout-sdk
```

## Setup

PayPal Checkout SDK is designed to simplify using the new PayPal checkout api in your app.

### Setup Credentials

Get client ID and client secret by going
to [https://developer.paypal.com/developer/applications](https://developer.paypal.com/developer/applications) and
generating a REST API app. Get Client ID and Secret from there.

### Setup a Paypal Client

Inorder to communicate with PayPal platform we need to set up a client first :

#### Create a client with sandbox environment :

```php
// import namespace
use PayPal\Checkout\Environment\SandboxEnvironment;
use PayPal\Checkout\Http\PayPalClient;

// client id and client secret retrieved from PayPal
$clientId = "<<PAYPAL-CLIENT-ID>>";
$clientSecret = "<<PAYPAL-CLIENT-SECRET>>";

// create a new sandbox environment
$environment = new SandboxEnvironment($clientId, $clientSecret);

// create a new client
$client = new PayPalClient($environment);
```

#### Create a client with production environment :

```php
// import namespace
use PayPal\Checkout\Environment\ProductionEnvironment;
use PayPal\Checkout\Http\PayPalClient;

// client id and client secret retrieved from PayPal
$clientId = "<<PAYPAL-CLIENT-ID>>";
$clientSecret = "<<PAYPAL-CLIENT-SECRET>>";

// create a new sandbox environment
$environment = new ProductionEnvironment($clientId, $clientSecret);

// create a new client
$client = new PayPalClient($environment);
```

## Usage

### Create an Order

```php
// Import namespace
use PayPal\Checkout\Http\OrderCreateRequest;
use PayPal\Checkout\Orders\AmountBreakdown;
use PayPal\Checkout\Orders\Item;
use PayPal\Checkout\Orders\Order;
use PayPal\Checkout\Orders\PurchaseUnit;

// Create a purchase unit with the total amount
$purchase_unit = new PurchaseUnit(AmountBreakdown::of('100.00'));

// Create & add item to purchase unit
$purchase_unit->addItem(Item::make('Item 1', '100.00', 'USD', 1));

// Create a new order with intent to capture a payment
$order = new Order();

// Add a purchase unit to order
$order->addPurchaseUnit($purchase_unit);

// Create an order create http request
$request = new OrderCreateRequest($order);

// Send request to PayPal
$response = $client->send($request);

// Parse result
$result = json_decode((string) $response->getBody());
echo $result->id; // id of the created order
echo $result->intent; // CAPTURE
echo $result->status; // CREATED
```

### Capture an Order

```php
// Import namespace
use PayPal\Checkout\Http\OrderCaptureRequest;

// Create an order capture http request
$request = new OrderCaptureRequest($order_id);

// Send request to PayPal
$response = $client->send($request);

// Parse result
$result = json_decode((string) $response->getBody());
echo $result->id; // id of the captured order
echo $result->status; // CAPTURED
```

## Changelog

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todo list.

## Security

If you discover any security related issues, please email author instead of using the issue tracker.

## Credits

- [PayPal Docs](https://developer.paypal.com/docs/)
- [Gitbook](https://www.gitbook.com/)

## License

license. Please see the [Licence](https://github.com/phpjuice/paypal-checkout-sdk/blob/master/LICENSE) for more
information.

![PHP Composer](https://github.com/phpjuice/paypal-checkout-sdk/workflows/PHP%20Composer/badge.svg?branch=master)
[![Build Status](https://travis-ci.com/phpjuice/paypal-checkout-sdk.svg?branch=master)](https://travis-ci.com/phpjuice/paypal-checkout-sdk)
[![Latest Stable Version](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/v)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk) 
[![Total Downloads](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/downloads)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
[![License](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/license)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
