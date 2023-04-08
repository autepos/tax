<?php

namespace Autepos\Tax\Contracts;

/**
 * Taxable interface.
 * e.g. a Product.
 */
interface Taxable
{
    /**
     * Get id.
     */
    public function getTaxableIdentifier(): string;

    /**
     * Get device type.
     */
    public function getTaxableType(): string;
}
