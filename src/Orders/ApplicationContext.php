<?php

namespace PayPal\Checkout\Orders;

use PayPal\Checkout\Concerns\CastsToJson;
use PayPal\Checkout\Contracts\Arrayable;
use PayPal\Checkout\Contracts\Jsonable;
use PayPal\Checkout\Exceptions\InvalidLandingPageException;
use PayPal\Checkout\Exceptions\InvalidShippingPreferenceException;
use PayPal\Checkout\Exceptions\InvalidUserActionException;

// landing_page
const LOGIN = 'LOGIN';
const BILLING = 'BILLING';
const NO_PREFERENCE = 'NO_PREFERENCE';

// shipping_preference
const GET_FROM_FILE = 'GET_FROM_FILE';
const NO_SHIPPING = 'NO_SHIPPING';
const SET_PROVIDED_ADDRESS = 'SET_PROVIDED_ADDRESS';

// user_action
const ACTION_CONTINUE = 'CONTINUE';
const ACTION_PAY_NOW = 'PAY_NOW';

/**
 * https://developer.paypal.com/docs/api/orders/v2/#definition-order_application_context.
 */
class ApplicationContext implements Arrayable, Jsonable
{
    use CastsToJson;

    /**
     * The label that overrides the business name in the PayPal account on the PayPal site.
     *
     * @var string|null
     */
    protected ?string $brand_name = null;

    /**
     * The BCP 47-formatted locale of pages that the PayPal payment experience shows.
     * PayPal supports a five-character code.
     * Examples : da-DK, he-IL, id-ID, ja-JP, no-NO, pt-BR.
     *
     * @var string
     */
    protected string $locale = 'en-US';

    /**
     * The type of landing page to show on the PayPal site for customer checkout. The possible values are:
     *
     * - LOGIN: When the customer clicks PayPal Checkout,
     *   the customer is redirected to a page to log in to PayPal and approve the payment.
     *
     * - BILLING: When the customer clicks PayPal Checkout,
     *   the customer is redirected to a page to enter credit or debit card
     *   and other relevant billing information required to complete the purchase.
     *
     * - NO_PREFERENCE: A decimal fraction for currencies like TND
     *   that are subdivided into thousandths.
     *
     * @var string
     */
    protected string $landing_page = NO_PREFERENCE;

    /**
     * The shipping preferences. The possible values are:
     *  - GET_FROM_FILE: Use the customer-provided shipping address on the PayPal site.
     *  - NO_SHIPPING: Redact the shipping address from the PayPal site. Recommended for digital goods.
     *  - SET_PROVIDED_ADDRESS: Use the merchant-provided address.
     *    The customer cannot change this address on the PayPal site.
     *
     * @var string
     */
    protected string $shipping_preference = NO_SHIPPING;

    /**
     * The URL where the customer is redirected after the customer approves the payment.
     *
     * @var string|null
     */
    protected ?string $return_url = null;

    /**
     * The URL where the customer is redirected after the customer cancels the payment.
     *
     * @var string|null
     */
    protected ?string $cancel_url = null;

    /**
     * Configures a Continue or Pay Now checkout flow. The possible values are:
     *
     * - CONTINUE: After you redirect the customer to the PayPal payment page,
     *   a Continue button appears. Use this option when the final amount
     *   is not known when the checkout flow is initiated, and you want
     *   to redirect the customer to the merchant page without processing the payment.
     *
     * - PAY_NOW: After you redirect the customer to the PayPal payment page,
     *   a Pay Now button appears,Use this option when the final amount is known
     *   when the checkout is initiated, and you want to process the payment
     *   immediately when the customer clicks Pay Now.
     *
     * @var string
     */
    protected string $user_action = ACTION_CONTINUE;

    /**
     * Create a new collection.
     *
     * @param  string|null  $brand_name
     * @param  string|null  $locale
     * @param  string|null  $landing_page
     * @param  string|null  $shipping_preference
     * @param  string|null  $return_url
     * @param  string|null  $cancel_url
     */
    public function __construct(
        ?string $brand_name = null,
        ?string $locale = 'en-US',
        ?string $landing_page = NO_PREFERENCE,
        ?string $shipping_preference = NO_SHIPPING,
        ?string $return_url = null,
        ?string $cancel_url = null
    ) {
        $this->setBrandName($brand_name);
        $this->setLocale($locale);
        $this->setLandingPage($landing_page);
        $this->setShippingPreference($shipping_preference);
        $this->setReturnUrl($return_url);
        $this->setCancelUrl($cancel_url);
    }

    /**
     * Create a new collection.
     *
     * @param  string|null  $brand_name
     * @param  string|null  $locale
     * @param  string|null  $landing_page
     * @param  string|null  $shipping_preference
     * @param  string|null  $return_url
     * @param  string|null  $cancel_url
     * @return ApplicationContext
     */
    public static function make(
        ?string $brand_name = null,
        ?string $locale = 'en-US',
        ?string $landing_page = NO_PREFERENCE,
        ?string $shipping_preference = NO_SHIPPING,
        ?string $return_url = null,
        ?string $cancel_url = null
    ): ApplicationContext {
        return new self(
            $brand_name,
            $locale,
            $landing_page,
            $shipping_preference,
            $return_url,
            $cancel_url
        );
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        $arrayable = [
            'brand_name' => $this->getBrandName() ?? null,
            'locale' => $this->getLocale() ?? null,
            'shipping_preference' => $this->getShippingPreference() ?? null,
            'landing_page' => $this->getLandingPage() ?? null,
            'user_action' => $this->getUserAction() ?? null,
            'return_url' => $this->getReturnUrl() ?? null,
            'cancel_url' => $this->getCancelUrl() ?? null,
        ];

        return array_filter(
            $arrayable,
            function ($item) {
                return null !== $item;
            }
        );
    }

    /**
     * gets brand_name.
     */
    public function getBrandName(): ?string
    {
        return $this->brand_name;
    }

    /**
     * sets the brand_name.
     */
    public function setBrandName($brand_name): self
    {
        $this->brand_name = $brand_name;

        return $this;
    }

    /**
     * gets brand_name.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * sets the locale.
     */
    public function setLocale($locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * gets shipping_preference.
     */
    public function getShippingPreference(): string
    {
        return $this->shipping_preference;
    }

    /**
     * sets the shipping_preference.
     * @noinspection PhpUnused
     */
    public function setShippingPreference($shipping_preference): self
    {
        $validOptions = [GET_FROM_FILE, NO_SHIPPING, SET_PROVIDED_ADDRESS];
        if (!in_array($shipping_preference, $validOptions)) {
            throw new InvalidShippingPreferenceException();
        }

        $this->shipping_preference = $shipping_preference;

        return $this;
    }

    /**
     * gets landing_page.
     * @noinspection PhpUnused
     */
    public function getLandingPage(): string
    {
        return $this->landing_page;
    }

    /**
     * sets the landing_page.
     */
    public function setLandingPage($landing_page): self
    {
        $validOptions = [LOGIN, BILLING, NO_PREFERENCE];
        if (!in_array($landing_page, $validOptions)) {
            throw new InvalidLandingPageException();
        }

        $this->landing_page = $landing_page;

        return $this;
    }

    /**
     * gets user_action.
     */
    public function getUserAction(): string
    {
        return $this->user_action;
    }

    /**
     * sets the user_action.
     * @noinspection PhpUnused
     */
    public function setUserAction($user_action): self
    {
        $validOptions = [ACTION_CONTINUE, ACTION_PAY_NOW];
        if (!in_array($user_action, $validOptions)) {
            throw new InvalidUserActionException();
        }
        $this->user_action = $user_action;

        return $this;
    }

    /**
     * gets return_url.
     * @noinspection PhpUnused
     */
    public function getReturnUrl(): ?string
    {
        return $this->return_url;
    }

    /**
     * sets the return_url.
     */
    public function setReturnUrl($return_url): self
    {
        $this->return_url = $return_url;

        return $this;
    }

    /**
     * gets cancel_url.
     */
    public function getCancelUrl(): ?string
    {
        return $this->cancel_url;
    }

    /**
     * sets the cancel_url.
     */
    public function setCancelUrl($cancel_url): self
    {
        $this->cancel_url = $cancel_url;

        return $this;
    }
}
