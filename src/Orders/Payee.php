<?php

namespace PayPal\Checkout\Orders;

use PayPal\Checkout\Concerns\CastsToJson;
use PayPal\Checkout\Contracts\Arrayable;
use PayPal\Checkout\Contracts\Jsonable;

/**
 * https://developer.paypal.com/docs/api/orders/v2/#definition-payee.
 */
class Payee implements Arrayable, Jsonable
{
    use CastsToJson;

    /**
     * The email address of merchant.
     *
     * @var string
     */
    protected string $email_address;

    /**
     * The encrypted PayPal account ID of the merchant.
     *
     * @var string
     */
    protected string $merchant_id;

    /**
     * create a new payee instance.
     */
    public function __construct(string $email_address, string $merchant_id)
    {
        $this->email_address = $email_address;
        $this->merchant_id = $merchant_id;
    }

    /**
     * create a new payee instance.
     */
    public static function make(string $email_address, string $merchant_id): Payee
    {
        return new self($email_address, $merchant_id);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'email_address' => $this->getEmailAddress(),
            'merchant_id' => $this->getMerchantId(),
        ];
    }

    /**
     * Gets payee email address.
     */
    public function getEmailAddress(): string
    {
        return $this->email_address;
    }

    /**
     * Sets payee email address.
     */
    public function setEmailAddress(string $email_address): self
    {
        $this->email_address = $email_address;

        return $this;
    }

    /**
     * Gets merchant id.
     */
    public function getMerchantId(): ?string
    {
        return $this->merchant_id;
    }

    /**
     * Sets payee merchant id.
     */
    public function setMerchantId(string $merchant_id): self
    {
        $this->merchant_id = $merchant_id;

        return $this;
    }
}
