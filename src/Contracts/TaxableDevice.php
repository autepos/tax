<?php

namespace Autepos\Tax\Contracts;

/**
 * Taxable interface.
 * e.g. a Cart/Order.
 */
interface TaxableDevice
{
    /**
     * Get id.
     */
    public function getTaxableDeviceIdentifier(): string;

    /**
     * Get device type.
     */
    public function getTaxableDeviceType(): string;

    /**
     * Get device tax calculator.
     *
     * @return array<mixed,TaxableDeviceLine>
     */
    public function getTaxableDeviceLines(): array;
}
