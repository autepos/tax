<?php

namespace Autepos\Tax;

use Autepos\AiPayment\Contracts\AddressData;
use Autepos\Tax\Contracts\TaxableDeviceLine;
use Autepos\Tax\Models\TaxRate;

class TaxLine
{
    /**
     * Tax device lines
     *
     * @var array<TaxableDeviceLine>
     */
    protected array $taxableDeviceLines = [];

    /**
     * Tax amount already included in price. This amount should not be
     * be added to order subtotal.
     */
    protected int $inclusiveAmount;

    /**
     * Tax amount not included in price. This amount needs to be added
     * to order subtotal.
     */
    protected int $exclusiveAmount;

    /**
     * Tax code
     */
    protected string $taxCode;

    /**
     * Tax rate
     */
    protected TaxRate $taxRate;

    /**
     * Address
     */
    protected AddressData $address;

    /**
     * TaxLine constructor.
     */
    public function __construct(int $exclusiveAmount, int $inclusiveAmount, string $taxCode, TaxRate $taxRate, AddressData $address, TaxableDeviceLine ...$taxableDeviceLines)
    {
        $this->exclusiveAmount = $exclusiveAmount;
        $this->inclusiveAmount = $inclusiveAmount;
        $this->taxCode = $taxCode;
        $this->taxRate = $taxRate;
        $this->address = $address;
        $this->taxableDeviceLines = $taxableDeviceLines;
    }

    /**
     * Get tax device lines
     *
     * @return array<TaxableDeviceLine>
     */
    public function getTaxDeviceLines(): array
    {
        return $this->taxableDeviceLines;
    }

    /**
     * Add tax device line
     */
    public function addTaxableDeviceLine(TaxableDeviceLine $taxableDeviceLine): void
    {
        $this->taxableDeviceLines[] = $taxableDeviceLine;
    }

    /**
     * Get the total of inclusive + exclusive amount. This amount should
     * not be added to order subtotal. It should only be used to display
     * the total tax amount.
     */
    public function totalAmount(): int
    {
        return $this->getInclusiveAmount() + $this->getExclusiveAmount();
    }

    /**
     * Get the value of exclusiveAmount
     */
    public function getExclusiveAmount()
    {
        return $this->exclusiveAmount;
    }

    /**
     * Set the value of exclusiveAmount
     */
    public function setExclusiveAmount($exclusiveAmount): self
    {
        $this->exclusiveAmount = $exclusiveAmount;

        return $this;
    }

    /**
     * Get the value of inclusiveAmount
     */
    public function getInclusiveAmount()
    {
        return $this->inclusiveAmount;
    }

    /**
     * Set the value of inclusiveAmount
     */
    public function setInclusiveAmount($inclusiveAmount): self
    {
        $this->inclusiveAmount = $inclusiveAmount;

        return $this;
    }

    /**
     * Get the value of taxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set the value of taxRate
     */
    public function setTaxRate($taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress(): AddressData
    {
        return $this->address;
    }

    /**
     * Get the value of taxCode
     */
    public function getTaxCode(): string
    {
        return $this->taxCode;
    }
}
