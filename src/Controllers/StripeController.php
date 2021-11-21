<?php

namespace Poupouxios\StripeLaravelWebhook\Controllers;

use App\Exceptions\StripeAuthException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookUserStripeAccountCreatedEvent;
use Poupouxios\StripeLaravelWebhook\Services\Stripe;
use Stripe\Checkout\Session;

if (class_exists("\\Illuminate\\Routing\\Controller")) {
    class BaseController extends \Illuminate\Routing\Controller {}
} elseif (class_exists("Laravel\\Lumen\\Routing\\Controller")) {
    class BaseController extends \Laravel\Lumen\Routing\Controller {}
}

class StripeController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create_user_account(Request $request)
    {
        Log::info("Stripe creating account", $request->all());
        $user = Auth::user();
        if ($user && $request->has('code')) {
            try {
                event(new StripeWebhookUserStripeAccountCreatedEvent(
                    app(Stripe::class)->authorizeAccount($request->get('code')),
                    $user->id,
                    $request->all())
                );
                flash(Config::get('stripe_webhooks.messages.success.create_stripe_user_account'))->success();
                return redirect(Config::get('stripe_webhooks.success_create_stripe_account_url'));
            } catch (StripeAuthException $exception) {
                Log::error('Stripe: Failed to authorize user after creation. Reason: ' . $exception->getMessage());
            }
        }
        flash(Config::get('stripe_webhooks.messages.errors.failed_create_stripe_user_account'))->error();
        return redirect('/');
    }

    /**
     * Call this create_checkout_session from your child Controller of Stripe to set the session object
     * @param Session $sesion_object
     * @return mixed
     */
    public function create_checkout_session(Session $sesion_object){
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                                        'message' => 'Session timeout. Refresh your browser to login again.'
                                    ], 400);
        }

        //this is mandatory so anyone using this package they need to implement their own checkout session in order
        //to create the StripeLineItemVO object and pass it to the createCheckoutSession of Stripe class. The return
        //object of createCheckoutSession must be passed to this create_checkout_session so we can catch it here and
        //send it back to UI
        if (isset($sesion_object)) {
            return response()->json([
                                        'message' => 'Checkout session link created. Redirecting to Checkout page..',
                                        'session_id' => $sesion_object->id
                                    ], 200);
        }

        return response()->json([
                                    'message' => 'Something went wrong. Our support team is notified.'
                                ], 500);
    }

    public function webhookAction(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');

        $event = null;

        // Verify webhook signature and extract the event.
        // See https://stripe.com/docs/webhooks/signatures for more information.
        try {
            $event = app(Stripe::class)->validateWebhookResponse($payload, $sig_header);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload.
            Log::error("Invalid payload: " . $e->getMessage());
            return response("", 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid Signature.
            Log::error("Invalid Signature: " . $e->getMessage());
            return response("", 400);
        }

        $eventType = str_replace([".", "_"], " ", $event->type);
        $eventProcessor = ucwords(strtolower($eventType)) . "Processor";
        $eventProcessor = str_replace(" ", "", $eventProcessor);

        Log::info("trying to access " . $eventProcessor);
        if (class_exists("\Poupouxios\StripeLaravelWebhook\Processors\\$eventProcessor")) {
            $processorClass = "\\Poupouxios\\StripeLaravelWebhook\\Processors\\$eventProcessor";
            $processor = new $processorClass;
            $processor->process($event);
        } else {
            Log::warning('Cannot find ' . "\Poupouxios\StripeLaravelWebhook\Processors\\$eventProcessor", [
                'payload' => $payload
            ]);
        }

        return response("", 200);
    }
}
