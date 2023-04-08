<?php

namespace Autepos\Tax\Tests\Fixtures;

use Autepos\AiPayment\Contracts\AddressData;
use Autepos\Tax\Contracts\Taxable;
use Autepos\Tax\Contracts\TaxableDeviceLine;

class TaxableDeviceLineFixture implements TaxableDeviceLine
{
    public ?AddressData $shippingAddress = null;

    public AddressData $billingAddress;

    public Taxable $taxable;

    /**
     * Constructor.
     */
    public function __construct(
        public string $id = '123',
        public string $type = 'order-line',
        public string $taxCode = 'TAX001',
        public int $amount = 100,
        public bool $amountInclusiveOfTax = false)
    {
        // Create a default Taxable object.
        $this->taxable = new TaxableFixture('12'.$this->id, '12'.$this->type);

        // Create a default shipping address.
        $this->shippingAddress = self::makeAddress('CA', 'BC');

        // Create a default billing address.
        $this->billingAddress = self::makeAddress('GB', 'London');
    }

    /**
     * Get id.
     */
    public function getTaxableDeviceLineIdentifier(): string
    {
        return $this->id;
    }

    /**
     * Get device type.
     */
    public function getTaxableDeviceLineType(): string
    {
        return $this->type;
    }

    public function getTaxableDeviceLineTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function getTaxableDeviceLineAmount(): int
    {
        return $this->amount;
    }

    public function isAmountInclusiveOfTax(): bool
    {
        return $this->amountInclusiveOfTax;
    }

    public function getTaxableDeviceLineBillingAddress(): AddressData
    {
        return $this->billingAddress;
    }

    public function getTaxableDeviceLineShippingAddress(): ?AddressData
    {
        return $this->shippingAddress;
    }

    /**
     * Set taxable.
     */
    public function setTaxable(Taxable $taxable)
    {
        $this->taxable = $taxable;
    }

    /**
     * Get taxable.
     */
    public function getTaxable(): Taxable
    {
        return $this->taxable ?? new TaxableFixture($this->id, $this->type);
    }

    /**
     * Set the value of shippingAddress
     *
     * @param  ?AddressData  $shippingAddress
     */
    public function setShippingAddress(?AddressData $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Set the value of billingAddress
     *
     * @param  AddressData  $billingAddress
     */
    public function setBillingAddress(?AddressData $billingAddress = null): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public static function makeAddress(string $countryCode, string $province): AddressData
    {
        return new AddressData([
            'country_code' => $countryCode,
            'province' => $province,
        ]);
    }
}
