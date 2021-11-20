<?php

namespace Poupouxios\StripeLaravelWebhook\Processors;

use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentSucceededCallbackEvent;

class PaymentIntentSucceededProcessor implements iPaymentProcessor
{
    public function process($event): bool
    {
        $payment_session = $event->data->object;
        Log::info("Stripe webhook payment_intent.succeeded", $event->toArray());
        $payment_intent_id = $payment_session->payment_intent ?? $payment_session->id;
        event(new StripeWebhookPaymentSucceededCallbackEvent($payment_intent_id, $payment_session));
        return true;
    }

}