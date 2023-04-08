<?php

namespace Autepos\Tax\Tests\Fixtures;

use Autepos\Tax\Contracts\TaxableDevice;
use Autepos\Tax\Contracts\TaxableDeviceLine;

class TaxableDeviceFixture implements TaxableDevice
{
    public $taxableDeviceLines = [];

    /**
     * Constructor
     *
     * @param  string  $taxCode If taxCode is provided, a taxable device line will be added with the taxCode.
     * @param  string  $amount If amount is provided, a taxable device line will be added with the amount.
     */
    public function __construct(
        public string $id = '345',
        public string $type = 'order',
        ?string $taxCode = null,
        ?int $amount = null,
    ) {
        if ($amount or $taxCode) {
            $this->taxableDeviceLines[] = new TaxableDeviceLineFixture('13'.$this->id, '13'.$this->type, $taxCode ?? 'TX001', $amount ?? 100);
        }
    }

    /**
     * Get id.
     */
    public function getTaxableDeviceIdentifier(): string
    {
        return $this->id;
    }

    /**
     * Get device type.
     */
    public function getTaxableDeviceType(): string
    {
        return $this->type;
    }

    /**
     * Set device lines.
     */
    public function setTaxableDeviceLines(TaxableDeviceLine ...$taxableDeviceLines): void
    {
        $this->taxableDeviceLines = $taxableDeviceLines;
    }

public function getTaxableDeviceLines(): array
{
    return $this->taxableDeviceLines;
}
}
