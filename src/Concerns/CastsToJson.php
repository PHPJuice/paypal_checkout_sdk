<?php

namespace PayPal\Checkout\Concerns;

use PayPal\Checkout\Exceptions\JsonEncodingException;

trait CastsToJson
{
    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     *
     * @return string
     *
     * @throws JsonEncodingException
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->toArray(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonEncodingException(json_last_error_msg());
        }

        return $json;
    }
}
