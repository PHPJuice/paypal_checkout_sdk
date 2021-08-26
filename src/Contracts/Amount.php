<?php

namespace PayPal\Checkout\Contracts;

interface Amount
{
    /**
     * convert amount to an array.
     * @return array
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function getCurrencyCode(): string;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string;
}
