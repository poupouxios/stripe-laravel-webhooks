<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when the payment has been cancelled by the user and Stripe will redirect you back to the cancel
 * page, which was provided during the Stripe Session creation.
 * Here you will get the session_id from the event and you will need to find the relevant stripe_user_id that was associated
 * during the creation of the redirect_url to make the payment. After that you can use the retrieveSessionIdData from app(Stripe::class)
 * to get the session object, which then you can pass it on the Poupouxios\StripeLaravelWebhook\Processors\PaymentIntentCanceledProcessor::process
 * method to trigger your StripeWebhookPaymentCancelledCallbackEvent to execute the code you set there.
 * Class StripeWebhookPaymentCancelledRedirectEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentCancelledRedirectEvent
{
    use Dispatchable, SerializesModels;

    /** @var int $session_id */
    public int $session_id;

    /** @var array $response_data */
    public array $response_data;

    /**
     * Create a new event instance.
     *
     * @param int $session_id
     * @param array $response_data
     */
    public function __construct(int $session_id, array $response_data)
    {
        $this->session_id = $session_id;
        $this->response_data = $response_data;
    }
}