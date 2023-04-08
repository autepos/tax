<?php

namespace Autepos\Tax\Tests;

use Autepos\Tax\Contracts\TaxCalculatorFactory;
use Autepos\Tax\TaxManager;
use Autepos\Tax\TaxServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.connections.mysql.engine', 'InnoDB');
    }

    /**
     * Get discount manager instance.
     */
    protected function taxManager(): TaxManager
    {
        return app(TaxCalculatorFactory::class);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TaxServiceProvider::class,
        ];
    }
}
