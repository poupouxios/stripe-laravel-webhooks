<?php

namespace Poupouxios\StripeLaravelWebhook\Providers;

use Illuminate\Support\ServiceProvider;
use Poupouxios\StripeLaravelWebhook\Services\Stripe;

class StripeRouteServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this, 'publishes')) {
            $this->publishes([
                                 __DIR__.'/../config/stripe_webhooks.php' => $this->config_path('stripe_webhooks.php'),
                             ]);

        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Stripe::class, function ($app) {
            return new Stripe();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    private function config_path($path = '')
    {
        return function_exists('config_path') ? config_path($path) : app()->basePath() . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

}
