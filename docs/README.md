# Introduction

This Package is a PHP SDK wrapper around version 2 of the PayPal rest API. It provides a simple, fluent API to create
and capture orders with both sandbox and production environments supported.

Here are some quick code examples:

## Create an Order

```php
// Create a purchase unit with the total amount
$purchase_unit = new PurchaseUnit(AmountBreakdown::of('100.00'));

// Create & add item to purchase unit
$purchase_unit->addItem(Item::make('Item 1', '100.00', 'USD', 1));

// Create a new order with intent to capture a payment
$order = (new Order())->addPurchaseUnit($purchase_unit);

// Send request to PayPal
$response = $client->send(new OrderCreateRequest($order));
```

## Capture an Order

```php
// Create an order capture http request
$request = new OrderCaptureRequest($order_id);

// Send request to PayPal
$response = $client->send($request);
```

## We have badges!

![PHP Composer](https://github.com/phpjuice/paypal-checkout-sdk/workflows/PHP%20Composer/badge.svg?branch=master)
[![Build Status](https://travis-ci.com/phpjuice/paypal-checkout-sdk.svg?branch=master)](https://travis-ci.com/phpjuice/paypal-checkout-sdk)
[![Latest Stable Version](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/v)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
[![Total Downloads](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/downloads)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
[![License](http://poser.pugx.org/phpjuice/paypal-checkout-sdk/license)](https://packagist.org/packages/phpjuice/paypal-checkout-sdk)
