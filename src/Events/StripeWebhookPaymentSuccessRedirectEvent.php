<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Queue\SerializesModels;

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