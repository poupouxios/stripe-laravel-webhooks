<?php

namespace Poupouxios\StripeLaravelWebhook\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Poupouxios\StripeLaravelWebhook\VO\StripeLineItemVO;

/**
 * This event is triggered when something went wrong during creating the Stripe session to redirect the user to make the
 * payment.
 * Here we pass the whole StripeLineItemVO object that was passed along with the error_message so the website owner can
 * record or make the necessary adjustments.
 * Class StripeWebhookFailedCreatingSessionEvent
 * @package Poupouxios\StripeLaravelWebhook\Events
 */
class StripeWebhookFailedCreatingSessionEvent
{
    use Dispatchable, SerializesModels;

    /** @var StripeLineItemVO $stripeLineItemVO */
    public StripeLineItemVO $stripeLineItemVO;

    /** @var string $error_message */
    public string $error_message;

    /**
     * Create a new event instance.
     *
     * @param StripeLineItemVO $stripeLineItemVO
     * @param string $error_message
     */
    public function __construct(StripeLineItemVO $stripeLineItemVO, string $error_message)
    {
        $this->stripeLineItemVO = $stripeLineItemVO;
        $this->error_message = $error_message;
    }
}