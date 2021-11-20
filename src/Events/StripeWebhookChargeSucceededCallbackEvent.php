<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is triggered when Stripe sends back the charge_succeeded event which was set on the Stripe Admin Dashboard.
 * This is the same event like the Payment Succeeded and we can use it in case the Payment Succeeded didn't arrive as a
 * callback. Here the transaction_id will be passed along with the whole response send from the callback.
 * The transaction_id can be used to find the pending payment created during the Session Creation and update the status
 * of the payment to succeeded.
 * Class StripeWebhookChargeSucceededCallbackEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookChargeSucceededCallbackEvent
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