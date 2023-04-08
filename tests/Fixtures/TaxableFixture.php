<?php

namespace Autepos\Tax\Tests\Fixtures;

use Autepos\Tax\Contracts\Taxable;

class TaxableFixture implements Taxable
{
    /**
     * Constructor
     */
    public function __construct(
        public string $id = '123',
        public string $type = 'product')
    {
    }

    /**
     * Get id.
     */
    public function getTaxableIdentifier(): string
    {
        return $this->id;
    }

    /**
     * Get device type.
     */
    public function getTaxableType(): string
    {
        return $this->type;
    }
}
