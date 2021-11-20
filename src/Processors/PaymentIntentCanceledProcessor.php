<?php

namespace Poupouxios\StripeLaravelWebhook\Processors;

use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentCancelledCallbackEvent;

class PaymentIntentCanceledProcessor implements iPaymentProcessor
{
    public function process($event): bool
    {
        Log::info("Stripe webhook payment_intent.canceled", $event->toArray());
        $payment_data = [];
        if (isset($event->data->object)) {
            $payment_data = $event->data->object;
            $payment_intent = $event->data->object->id;
        } elseif (isset($event->payment_intent)) {
            $payment_data = $event;
            $payment_intent = $event->payment_intent;
        } else {
            Log::error('Payment cannot be found: ', $event->toArray());
            return false;
        }

        event(new StripeWebhookPaymentCancelledCallbackEvent($payment_intent, $payment_data));
        return true;
    }
}