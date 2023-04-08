<?php

namespace Autepos\Tax;

use Autepos\Tax\Calculators\Contracts\TaxCalculator;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class TaxManager extends Manager implements Contracts\TaxCalculatorFactory
{
    /**
     * Get the default driver/calculator name.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No tac calculator was specified.');
    }

    /**
     * {@inheritDoc}
     */
    public function calculator(string $calculator = null): TaxCalculator
    {
        return parent::driver($calculator);
    }
}
