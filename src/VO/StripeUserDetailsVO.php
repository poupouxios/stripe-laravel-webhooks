<?php

namespace Poupouxios\StripeLaravelWebhook\VO;

class StripeUserDetailsVO
{
    /** @var string $client_id */
    public string $client_id;

    /** @var string $csrf_token */
    public string $csrf_token;

    /** @var string $email */
    public string $email = "";

    /** @var string $url */
    public string $url = "";

    /** @var string $form_key */
    public string $business_name = "";

    /** @var string $first_name */
    public string $first_name = "";

    /** @var string $last_name */
    public string $last_name = "";

    /** @var string $street_address */
    public string $street_address = "";

    /** @var string $city */
    public string $city = "";

}