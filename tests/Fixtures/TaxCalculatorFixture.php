<?php

namespace Autepos\Tax\Tests\Fixtures;

use Autepos\Tax\Calculators\Contracts\TaxCalculator;

/**
 * TaxCalculatorFixture class.
 */
class TaxCalculatorFixture extends TaxCalculator
{
    const CALCULATOR = 'generic';

    const VERSION = '1.0.0';

    const DESCRIPTION = 'Generic tax calculator';

    const AUTHOR = 'Autepos';

    /**
     * Get calculator name
     */
    public function getCalculator(): string
    {
        return static::CALCULATOR;
    }

    /**
     * Get calculator version
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get calculator description
     */
    public function getDescription(): string
    {
        return static::DESCRIPTION;
    }

    /**
     * Get calculator author
     */
    public function getAuthor(): string
    {
        return static::AUTHOR;
    }
}
