<?php

namespace PayPal\Checkout\Exceptions;

use RuntimeException;

class InvalidOrderException extends RuntimeException
{
    public static function invalidPurchaseUnit(): InvalidOrderException
    {
        return new self("Paypal orders must have 1 purchase_unit at least.");
    }
}
