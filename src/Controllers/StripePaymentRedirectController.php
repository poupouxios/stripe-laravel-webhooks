<?php
namespace Poupouxios\StripeLaravelWebhook\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentCancelledRedirectEvent;
use Poupouxios\StripeLaravelWebhook\Events\StripeWebhookPaymentSuccessRedirectEvent;

if (class_exists("\\Illuminate\\Routing\\Controller")) {
    class BaseController extends \Illuminate\Routing\Controller {}
} elseif (class_exists("Laravel\\Lumen\\Routing\\Controller")) {
    class BaseController extends \Laravel\Lumen\Routing\Controller {}
}

class StripePaymentRedirectController  extends BaseController
{
    /**
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function payment_success(Request $request)
    {
        Log::info("Stripe Payment success : ", $request->all());
        event(new StripeWebhookPaymentSuccessRedirectEvent($request->all()));
        $current_url = Config::get("stripe_webhooks.redirect_url");
        flash(Config::get("stripe_webhooks.messages.success.payment_success"))->success()->important();
        if (empty($current_url)) {
            $current_url = "/";
        }
        return redirect($current_url);
    }

    /**
     * @param Request $request
     * @return RedirectResponse||void
     */
    public function payment_cancel(Request $request)
    {
        Log::info("Payment cancel : ", $request->all());
        $session_id = $request->get('session_id');
        event(new StripeWebhookPaymentCancelledRedirectEvent($session_id, $request->all()));
        flash(Config::get("stripe_webhooks.messages.errors.payment_cancelled"))->error();
        $current_url = Config::get("stripe_webhooks.redirect_url");
        return redirect($current_url);
    }

}