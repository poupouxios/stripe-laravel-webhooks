<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Poupouxios\StripeLaravelWebhook\VO\StripeLineItemVO;

/**
 * This event is triggered when the session is successfully created from Stripe and we can redirect on payment page.
 * As a website owner, you need to store the session->id and the $stripeLineItemVO->$stripe_user_id because this
 * will be needed during the PaymentCancelledRedirect Event in order to mark a payment as cancelled.
 * Class StripeWebhookPaymentPendingCreationEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookPaymentPendingCreationEvent
{
    use Dispatchable, SerializesModels;

    public $stripe_session;

    /** @var StripeLineItemVO $stripeLineItemVO */
    public $stripeLineItemVO;

    /**
     * Create a new event instance.
     *
     * @param $stripe_session
     * @param StripeLineItemVO $stripeLineItemVO
     */
    public function __construct($stripe_session, StripeLineItemVO $stripeLineItemVO)
    {
        $this->stripe_session = $stripe_session;
        $this->stripeLineItemVO = $stripeLineItemVO;
    }
}