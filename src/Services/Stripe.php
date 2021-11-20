<?php

namespace Poupouxios\StripeLaravelWebhook\Services;

use Poupouxios\StripeLaravelWebhook\Exceptions\StripeAuthException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookFailedCreatingSessionEvent;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentPendingCreationEvent;
use Poupouxios\StripeLaravelWebhook\VO\StripeLineItemVO;
use Poupouxios\StripeLaravelWebhook\VO\StripeUserDetailsVO;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\OAuth;
use Stripe\Webhook;

class Stripe
{
    /** @var PaymentRepository $paymentRepository */
    private $paymentRepository = null;

    public function __construct()
    {
        \Stripe\Stripe::setApiKey(Config::get('services.stripe.secret'));
    }

    /**
     * @param $authorization_code
     * @return mixed|null
     * @throws StripeAuthException
     * @throws \Stripe\Exception\OAuth\OAuthErrorException
     */
    public function authorizeAccount($authorization_code)
    {
        $response = OAuth::token(
            [
                'grant_type' => 'authorization_code',
                'code' => $authorization_code,
            ]
        );

        if (isset($request->error)) {
            throw new StripeAuthException($response->error . " " . $response->error_description);
        }

        Log::info('Stripe success validation', $response->toArray());

        return $response->stripe_user_id;
    }

    /**
     * @param StripeUserDetailsVO $stripeUserDetailsVO
     * @return string
     */
    public function buildConnectToStripeUrl(StripeUserDetailsVO $stripeUserDetailsVO)
    {
        $url = "https://connect.stripe.com/oauth/authorize?state=" . $stripeUserDetailsVO->csrf_token . "&scope=read_write&response_type=code&client_id="
            . $stripeUserDetailsVO->client_id;

        $extra_params = [
            'stripe_user[email]' => $stripeUserDetailsVO->email ?? "",
            'stripe_user[url]' => $stripeUserDetailsVO->website ?? "",
            'stripe_user[business_name]' => $stripeUserDetailsVO->company_name ?? "",
            'stripe_user[first_name]' => $stripeUserDetailsVO->first_name ?? "",
            'stripe_user[last_name]' => $stripeUserDetailsVO->last_name ?? "",
            'stripe_user[street_address]' => $stripeUserDetailsVO->company_address ?? "",
            'stripe_user[city]' => $stripeUserDetailsVO->name ?? "",
        ];

        $url .= "&" . http_build_query($extra_params);
        return $url;
    }

    /**
     * Retrieve the session id object to get the payment intent id
     * @param $session_id
     * @param $vendor_stripe_account
     * @return Session|null
     */
    public function retrieveSessionIdData($session_id, $vendor_stripe_account)
    {
        try {
            return Session::retrieve(
                $session_id,
                ['stripe_account' => $vendor_stripe_account]
            );
        } catch (\Exception $exception) {
            Log::error("Failed to retrieve session data (session id $session_id)", [$exception->getMessage()]);
        }
        return null;
    }

    /**
     * Generate the payment Intent to initiate the process for payment
     * @param StripeLineItemVO $stripeLineItemVO
     * @return Session|null
     */
    public function createCheckoutSession(StripeLineItemVO $stripeLineItemVO)
    {
        if (!empty($stripeLineItemVO->stripe_user_id)) {
            try {
                $session = Session::create(
                    [
                        'payment_method_types' => ['card'],
                        'line_items' => [
                            [
                                'name' => $stripeLineItemVO->name,
                                'amount' => round($stripeLineItemVO->amount, 2) * 100,
                                'images' => [$stripeLineItemVO->image_url],
                                'currency' => 'eur',
                                'quantity' => 1,
                            ]
                        ],
                        'mode' => 'payment',
                        'success_url' => route(
                                Config::get('stripe_webhooks.payment.success_route_name')
                            ) . "?session_id={CHECKOUT_SESSION_ID}",
                        'cancel_url' => route(
                                Config::get('stripe_webhooks.payment.cancel_route_name')
                            ) . "?session_id={CHECKOUT_SESSION_ID}",
                    ],
                    ['stripe_account' => $stripeLineItemVO->stripe_user_id]
                );
                if ($stripeLineItemVO->application_fee > 0) {
                    $session['payment_intent_data'] = [
                        'application_fee_amount' => round($stripeLineItemVO->application_fee, 2) * 100,
                    ];
                }
                if (isset($session->id)) {
                    event(new StripeWebhookPaymentPendingCreationEvent($session, $stripeLineItemVO));
                }

                Log::info("Checkout session data ", $session->toArray());
                return $session;
            } catch (\Exception $e) {
                event(new StripeWebhookFailedCreatingSessionEvent($stripeLineItemVO, $e->getMessage()));
                return null;
            }
        }
        event(
            new StripeWebhookFailedCreatingSessionEvent($stripeLineItemVO, "Stripe User Id not found or not set.")
        );
        return null;
    }

    /**
     * @param $payload
     * @param $sig_header
     * @param $webhook_secret
     * @return Event
     * @throws SignatureVerificationException
     */
    public function validateWebhookResponse($payload, $sig_header, $webhook_secret)
    {
        return Webhook::constructEvent(
            $payload,
            $sig_header,
            $webhook_secret
        );
    }

}
