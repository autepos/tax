<?php

namespace Autepos\Tax\Contracts;

use Autepos\Tax\Calculators\Contracts\TaxCalculator;

interface TaxCalculatorFactory
{
    /**
     * Get a tax calculator implementation.
     */
    public function calculator(string $calculator = null): TaxCalculator;
}
