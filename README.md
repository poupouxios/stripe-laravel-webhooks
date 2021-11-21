# Laravel Stripe Webhooks

A stripe package for Laravel to have the basic routes for creating a link and also Events to be triggered when callbacks come from Stripe

## Install (Laravel)

* Add in your composer.json the below repository to be able to pull the package from github
```bash
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/poupouxios/stripe-laravel-webhooks"
    }
  ]
```

* Install via composer
```bash
composer require poupouxios/stripe-laravel-webhooks
```

* Add Service Provider to `config/app.php` in `providers` section
```php
Poupouxios\StripeLaravelWebhook\Providers\StripeRouteServiceProvider::class,
```

* Run the following command to publish configuration:

```bash
php artisan vendor:publish --provider "Poupouxios\StripeLaravelWebhook\Providers\StripeRouteServiceProvider"
```

Add the redirect and callback routes in your web routes file:
```php 
Route::get('payment/success', '\Poupouxios\StripeLaravelWebhook\Controllers\StripePaymentRedirectController@payment_success')->name('stripe_payment_success');
Route::get('payment/cancel', '\Poupouxios\StripeLaravelWebhook\Controllers\StripePaymentRedirectController@payment_cancel')->name('stripe_payment_cancel');
Route::get('stripe/user-account', '\Poupouxios\StripeLaravelWebhook\Controllers\StripeController@create_user_account');
/** Stripe webbook */
Route::post('stripe/webhook', '\Poupouxios\StripeLaravelWebhook\Controllers\StripeController@webhookAction');
```

The config `stripe_webhooks.php` will be automatically published on the `config/stripe_webhooks.php`. In case you changed the Route name for `payment/success` or `payment/cancel` 
you need to change it also on `stripe_webhooks.php` config file under 
```php
'payment' => [
        'success_route_name' => 'stripe_payment_success',
        'cancel_route_name' => 'stripe_payment_cancel'
],
...
```

## Usage

This library supports the below routes that Stripe needs to have a site to make payments, create a stripe account and get the callbacks on some basic events:
- **Payment Success page**
    - a route that can be passed during redirecting to Stripe paywall to make the payment in order when a successful payment is done to redirect the user back to that success page
- **Payment Cancel page**
    - a route that can be passed during redirecting to Stripe paywall to make the payment. In case of cancel Stripe will redirect the user back to this cancel route
- **Create Stripe Account**
    - this route is applicable for businesses that are connecting other businesses through the website to the end user. This will be used to create to the registered Business a Stripe account and the payment is done directly to the business rather the website owner
- **Stripe Webhook route**
    - this route will receive all the triggered events that are setup on the website owner Stripe account. Here are all the possible events that can be setup https://stripe.com/docs/api/events/types

The minimum supported events that the library has are (on each event in parentheses are the properties passed through the triggered event):
  - **StripeWebhookChargeSucceededCallbackEvent($transaction_id,$callback_response)**
    - This event is triggered when Stripe sends back the charge_succeeded event which was set on the Stripe Admin Dashboard. This is the same event like the "Payment Succeeded" and we can use it in case the Payment Succeeded didn't arrive as a  callback. Here the transaction_id will be passed along with the whole response send from the callback.
    - The transaction_id can be used to find the pending payment created during the Session Creation and update the status of the payment to succeed.
  - **StripeWebhookFailedCreatingSessionEvent (StripeLineItemVO $stripeLineItemVO, $error_message)**
    - This event is triggered when something went wrong during creating the Stripe session to redirect the user to make the payment.
    - Here we pass the whole StripeLineItemVO object that was passed along with the "error_message", so the website owner can record or make the necessary adjustments
  - **StripeWebhookPaymentCancelledCallbackEvent ($transaction_id,$callback_response)**
    - This event is triggered when a Cancel Payment was done from the user and the user was redirect back to the site.
    - This event is used as a safety in cases the redirection to cancel page was throwing an error or something didn't go well.
    - As a website owner, you should check first if the payment is already marked as cancelled/failed and if not make the necessary changes.
  - **StripeWebhookPaymentCancelledRedirectEvent ($session_id, $response_data)**
    - This event is triggered when the payment has been cancelled by the user and Stripe will redirect you back to the cancel page, which was provided during the Stripe Session creation.
    - Here you will get the session_id from the event and you will need to find the relevant stripe_user_id that was associated during the creation of the redirect_url to make the payment. After that you can use the retrieveSessionIdData from app(Stripe::class) to get the session object, which then you can pass it on the Poupouxios\StripeLaravelWebhook\Processors\PaymentIntentCanceledProcessor::process  method to trigger your StripeWebhookPaymentCancelledCallbackEvent to execute the code you set there.
  - **StripeWebhookPaymentCreatedCallbackEvent ($transaction_id,$callback_response)**
    - This event is triggered when the session is created successfully and user is redirected to the Paywall to pay.
    - This can be useful to record in cases you want to know that user reach the paywall page to make the payment but didn't do anything else or use it to do some processing on background.
  - **StripeWebhookPaymentFailedCallbackEvent ($transaction_id,$callback_response)**
    - This event is triggered when a payment was failed during various reasons. The transaction_id will be provided which was initially created during the Session Creation along with the whole response Stripe send back.
  - **StripeWebhookPaymentPendingCreationEvent ($stripe_session, StripeLineItemVO $stripeLineItemVO)**
    - This event is triggered when the session is successfully created from Stripe and we can redirect on payment page.
    - As a website owner, you need to store the session->id and the $stripeLineItemVO->$stripe_user_id because this will be needed during the PaymentCancelledRedirect Event in order to mark a payment as cancelled.
  - **StripeWebhookPaymentSucceededCallbackEvent ($transaction_id,$callback_response)**
    - This event is triggered when Stripe sends back the payment_succeeded event which was set on the Stripe Admin Dashboard.
    - This is the same event like the Charge Succeeded and we can use it in case the Charge Succeeded didn't arrive as a callback. Here the transaction_id will be passed along with the whole response send from the callback.
    - The transaction_id can be used to find the pending payment created during the Session Creation and update the status of the payment to succeed.
  - **StripeWebhookPaymentSuccessRedirectEvent ($callback_response)**
    - This event is triggered when the user is redirected back to the success page. The whole response is send, so the website owner can make the necessary changes on his logic.
  - **StripeWebhookUserStripeAccountCreatedEvent ($stripe_user_id, $user_id, array $response_data)**
    - This event is triggered when a user had setup successfully an account on Stripe and the redirection to the create-account url was done.
    - This is used in cases like website have vendors that register to Stripe and want the end-user to buy their products.
    - This way you connect the vendor with user and you can get a commission from this. An example is Uber with taxi-driver and the end customer.
    - As a website owner, you should store the stripe_user_id along with the user_id that created the account, so you can use it during checkout to make the necessary payment to the relevant user.
    - The whole response data are also send back.
  
All the above are also having some logs in place eg. in Payment Failed callback we have `Log::info("Stripe webhook payment_intent.payment_failed", $event->toArray());`

In case a callback comes and that event doesn't exist it will appear on logs eg. `Cannot find \Poupouxios\StripeLaravelWebhook\Processors\CheckoutSessionCompletedProcessor` and the payload will be passed so you don't lose the info.

To setup the above Laravel events on your website you need to setup on your `EventServiceProvider` your own listeners. An example is:

```php
    protected $listen = [
        StripeWebhookPaymentSucceededCallbackEvent::class => [
            SWPaymentSucceededListener::class
        ],
        StripeWebhookPaymentCancelledRedirectEvent::class => [
            SWPaymentCancelledRedirectListener::class
        ],
        StripeWebhookChargeSucceededCallbackEvent::class => [
            SWPaymentSucceededListener::class
        ],
        StripeWebhookPaymentCancelledCallbackEvent::class => [
            SWPaymentCancelledListener::class
        ],
        StripeWebhookPaymentFailedCallbackEvent::class => [
            SWPaymentFailedListener::class
        ],
        StripeWebhookPaymentPendingCreationEvent::class => [
            SWPaymentPendingCreationListener::class
        ],
        StripeWebhookUserStripeAccountCreatedEvent::class => [
            SWUserStripeAccountCreatedListener::class
        ],
        StripeWebhookFailedCreatingSessionEvent::class => [
            SWFailedCreatingSessionListener::class
        ],
        StripeWebhookPaymentSuccessRedirectEvent::class => [
            SWPaymentSuccessRedirectListener::class
        ]
    ];
```
