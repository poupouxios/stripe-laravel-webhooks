<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Redirect
    |--------------------------------------------------------------------------
    |
    | Any data needed during success or cancel of a payment
    |
    */
    'redirect_url' => "/",
    'success_create_stripe_account_url' => "profile",

    /*
    |--------------------------------------------------------------------------
    | Checkout session
    |--------------------------------------------------------------------------
    |
    | Any data needed during checkout
    |
    */

    'payment' => [
        'success_route_name' => 'stripe_payment_success',
        'cancel_route_name' => 'stripe_payment_cancel'
    ],

    'messages' => [
        'errors' => [
            'failed_create_stripe_user_account' => 'Something went wrong with Stripe User Account Creation. Please contact our support for more info.',
            'payment_cancelled' => 'Payment has been canceled'
        ],
        'success' => [
            'create_stripe_user_account' => 'Stripe Account has been created successfully'
        ]
    ],

    'webhook_secret' => '',
    'stripe_secret' => '',

];
