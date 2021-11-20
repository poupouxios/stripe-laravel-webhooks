# Laravel Stripe Webhooks

A stripe package for Laravel to have the basic routes for creating a link and also Events to be triggered when callbacks come from Stripe

## Install (Laravel)
Install via composer
```bash
composer require poupouxios/stripe-laravel-webhooks
```

Add Service Provider to `config/app.php` in `providers` section
```php
Poupouxios\StripeLaravelWebhook\Providers\StripeRouteServiceProvider::class,
```

Add the redirect and callback routes in your web routes file:
```php 
Route::get('payment/success', 'Poupouxios\StripeLaravelWebhook\Controllers\StripePaymentRedirectController@payment_success')->name('stripe_payment_success');
Route::get('payment/cancel', 'Poupouxios\StripeLaravelWebhook\Controllers\StripePaymentRedirectController@payment_cancel')->name('stripe_payment_cancel');
Route::get('stripe/user-account', 'Poupouxios\StripeLaravelWebhook\Controllers\StripeController@create_user_account');
/** Stripe webbook */
Route::post('stripe/webhook', 'Poupouxios\StripeLaravelWebhook\Controllers\StripeController@webhookAction');
```

The config `stripe_webhooks.php` will be automatically published on the `config/stripe_webhooks.php`. In case you changed the Route name for `payment/success` or `payment/cancel` 
you need to change it also on `stripe_webhooks.php` config file under 
```php
...
'payment' => [
        'success_route_name' => 'stripe_payment_success',
        'cancel_route_name' => 'stripe_payment_cancel'
],
...
```