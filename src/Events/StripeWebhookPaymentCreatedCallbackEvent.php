<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when the session is created successfully and user is redirected to the Paywall to pay.
 * This can be useful to record in cases you want to know that user reach the paywall page to make the payment but didn't
 * do anything else or use it to do some processing on background.
 * Class StripeWebhookPaymentCreatedCallbackEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentCreatedCallbackEvent
{
    use Dispatchable, SerializesModels;

    public $transaction_id;

    public $callback_response;

    /**
     * Create a new event instance.
     *
     * @param $transaction_id
     * @param $callback_response
     */
    public function __construct($transaction_id,$callback_response)
    {
        $this->transaction_id = $transaction_id;
        $this->callback_response = $callback_response;
    }
}