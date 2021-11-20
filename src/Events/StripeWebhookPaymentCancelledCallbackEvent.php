<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when a Cancel Payment was done from the user and the user was redirect back to the site.
 * This event is used as a safety in cases the redirection to cancel page was throwing an error or something didn't go well.
 * As a website owner, you should check first if the payment is already marked as cancelled/failed and if not execute make
 * the necessary changes.
 * Class StripeWebhookPaymentCancelledCallbackEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentCancelledCallbackEvent
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