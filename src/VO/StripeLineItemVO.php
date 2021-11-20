<?php

namespace Poupouxios\StripeLaravelWebhook\VO;

class StripeLineItemVO
{
    /** @var string $name */
    public string $name;

    /** @var string $image_url */
    public string $image_url;

    /** @var float $amount */
    public float $amount;

    /** @var string $currency */
    public string $currency;

    /** @var int $quantity */
    public int $quantity;

    /** @var int $application_fee */
    public int $application_fee = 0;

    /** @var string $stripe_user_id */
    public string $stripe_user_id;

    /** @var array $extra_parameters  */
    public array $extra_parameters = [];
}