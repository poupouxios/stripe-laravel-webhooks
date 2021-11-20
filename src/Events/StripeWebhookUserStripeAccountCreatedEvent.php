<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when a user had setup successfully an account on Stripe and the redirection to the create-account
 * url was done.
 * This is used in cases like website have vendors that register to Stripe and want the end-user to buy their products.
 * This way you connect the vendor with user and you can get a commission from this. An example is Uber with taxi-driver and the
 * end customer.
 * As a website owner, you should store the stripe_user_id along with the user_id that created the account
 * so you can use it during checkout to make the necessary payment to the relevant user.
 * The whole response data are also send back.
 * Class StripeWebhookUserStripeAccountCreatedEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookUserStripeAccountCreatedEvent
{
    use Dispatchable, SerializesModels;

    /** @var string $stripe_user_id */
    public string $stripe_user_id;

    /** @var int $user_id */
    public int $user_id;

    /** @var array $response_data */
    public array $response_data;

    /**
     * Create a new event instance.
     *
     * @param int $stripe_user_id
     * @param int $user_id
     * @param array $response_data
     */
    public function __construct(int $stripe_user_id, int $user_id, array $response_data)
    {
        $this->stripe_user_id = $stripe_user_id;
        $this->user_id = $user_id;
        $this->response_data = $response_data;
    }
}