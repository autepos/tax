<?php

namespace Autepos\Tax\Contracts;

use Autepos\AiPayment\Contracts\AddressData;

/**
 * Taxable interface.
 * e.g. a Cart line item or Order line item.
 */
interface TaxableDeviceLine
{
    /**
     * Get id.
     */
    public function getTaxableDeviceLineIdentifier(): string;

    /**
     * Get device type.
     */
    public function getTaxableDeviceLineType(): string;

    /**
     * Get device line tax code.
     */
    public function getTaxableDeviceLineTaxCode(): ?string;

    /**
     * Get device line amount. This the amount that should be taxed.
     * E.g., the subtotal of a line item after discounts and before tax.
     */
    public function getTaxableDeviceLineAmount(): int;

    /**
     * Get tax behavior.
     */
    public function isAmountInclusiveOfTax(): bool;

    /**
     * Get billing address.
     */
    public function getTaxableDeviceLineBillingAddress(): AddressData;

    /**
     * Get shipping address.
     */
    public function getTaxableDeviceLineShippingAddress(): ?AddressData;

    /**
     * Get the underlying Taxable.
     */
    public function getTaxable(): Taxable;
}
