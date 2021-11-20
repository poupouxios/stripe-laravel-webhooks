<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when a payment was failed during various reasons. The transaction_id will be provided which was
 * initially created during the Session Creation along with the whole response Stripe send back.
 * Class StripeWebhookPaymentFailedCallbackEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentFailedCallbackEvent
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