<?php

namespace Autepos\Tax;

use Autepos\Tax\Calculators\GenericTaxCalculator;
use Autepos\Tax\Contracts\TaxCalculatorFactory;
use Illuminate\Support\ServiceProvider;

class TaxServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TaxCalculatorFactory::class, function ($app) {
            return new TaxManager($app);
        });
    }

    /**
     * Boot he service provider
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Register default discount processor
         */
        $paymentManager = $this->app->make(TaxCalculatorFactory::class);

        $paymentManager->extend(GenericTaxCalculator::CALCULATOR, function ($app) {
            return $app->make(GenericTaxCalculator::class);
        });

        /**
         * Load and publish
         */
        if ($this->app->runningInConsole()) {
            //
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            //
            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'autepos-tax-migrations');
        }
    }
}
