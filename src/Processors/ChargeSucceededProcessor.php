<?php

namespace Poupouxios\StripeLaravelWebhook\Processors;

use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookChargeSucceededCallbackEvent;

class ChargeSucceededProcessor extends PaymentIntentSucceededProcessor
{
    public function process($event): bool
    {
        $payment_session = $event->data->object;
        Log::info("Stripe webhook charge.succeeded", $event->toArray());
        $payment_intent_id = $payment_session->payment_intent ?? $payment_session->id;
        event(new StripeWebhookChargeSucceededCallbackEvent($payment_intent_id, $payment_session));
        return true;
    }
}