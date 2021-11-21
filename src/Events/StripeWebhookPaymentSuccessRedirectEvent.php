<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when the user is redirected back to the success page. The whole response is send so the website owner
 * can make the necessary changes on his logic.
 * Class StripeWebhookPaymentSuccessRedirectEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentSuccessRedirectEvent
{
    use Dispatchable, SerializesModels;

    public $callback_response;

    /**
     * Create a new event instance.
     *
     * @param $callback_response
     */
    public function __construct($callback_response)
    {
        $this->callback_response = $callback_response;
    }
}