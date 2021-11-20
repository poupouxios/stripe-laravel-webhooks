<?php

namespace Poupouxios\StripeLaravelWebhook\Processors;

use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentCreatedCallbackEvent;

class PaymentIntentCreatedProcessor implements iPaymentProcessor
{
    /**
     * @param $event
     * @return bool|void
     */
    public function process($event): bool
    {
        Log::info("Stripe webhook payment_intent.created", $event->toArray());
        $payment_session = $event->data->object ?? $event;
        $payment_intent_id = $payment_session->payment_intent ?? $payment_session->id;
        event(new StripeWebhookPaymentCreatedCallbackEvent($payment_intent_id, $payment_session));
        return true;
    }
}