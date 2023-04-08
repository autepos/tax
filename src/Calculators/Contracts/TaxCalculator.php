<?php

namespace Autepos\Tax\Calculators\Contracts;

use Autepos\AiPayment\Contracts\AddressData;
use Autepos\Tax\Contracts\TaxableDevice;
use Autepos\Tax\Contracts\TaxableDeviceLine;
use Autepos\Tax\Exceptions\TaxRateNotFoundException;
use Autepos\Tax\Models\TaxRate;
use Autepos\Tax\TaxableDeviceLineGrouper;
use Autepos\Tax\TaxLine;
use Autepos\Tax\TaxLineList;

/**
 * Tax calculator template.
 */
abstract class TaxCalculator
{
    /**
     * Taxable devices.
     *
     * @var array<int,TaxableDevice>
     */
    protected array $taxableDevices = [];

    /**
     * Taxable device line grouper.
     */
    protected TaxableDeviceLineGrouper $taxableDeviceLineGrouper;

    /**
     * Tenant id.
     */
    protected ?string $tenantId = null;

    /**
     * Group tax lines by tax code.
     *
     * @var bool
     */
    protected $groupByTaxCode = true;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->taxableDeviceLineGrouper = new TaxableDeviceLineGrouper();
    }

    /**
     * Get calculator name
     */
    abstract public function getCalculator(): string;

    /**
     * Get calculator version
     */
    abstract public function getVersion(): string;

    /**
     * Get calculator description
     */
    abstract public function getDescription(): string;

    /**
     * Get calculator author
     */
    abstract public function getAuthor(): string;

    /**
     * Add taxable device.
     */
    public function addTaxableDevice(TaxableDevice ...$taxableDevice): static
    {
        $this->taxableDevices = array_merge($this->taxableDevices, $taxableDevice);

        return $this;
    }

    /**
     * Calculate taxes.
     */
    public function calculate(): TaxLineList
    {
        $taxLineList = new TaxLineList();
        foreach ($this->taxableDevices as $taxableDevice) {
            $taxableDeviceLines = $taxableDevice->getTaxableDeviceLines();
            $this->validateTaxableDeviceLines($taxableDeviceLines);

            $taxableDeviceLineByCountries = $this->taxableDeviceLineGrouper->byCountryCode(...$taxableDeviceLines);
            foreach ($taxableDeviceLineByCountries as $taxableDeviceLineByCountry) {
                $taxableDeviceLineByProvinces = $this->taxableDeviceLineGrouper->byProvince(...$taxableDeviceLineByCountry);
                foreach ($taxableDeviceLineByProvinces as $taxableDeviceLineByProvince) {
                    $taxableDeviceLineGroups = $this->groupByTaxCode
                        ? $this->taxableDeviceLineGrouper->byTaxCode(...$taxableDeviceLineByProvince)
                        : [$taxableDeviceLineByProvince];
                    foreach ($taxableDeviceLineGroups as $taxableDeviceLineGroup) {
                        /**
                         * We can associate a tax rate with each taxable device line in the
                         * group. However, we only need to do this for one of the lines in
                         * the group because we do not a expect a different tax rate for
                         * for items with the same tax code if they have the same address
                         * and tenant id. Therefore, we will use the first line in the group
                         * to find the tax rate for all lines.
                         */
                        $taxableDeviceLineRep = $taxableDeviceLineGroup[0];
                        $address = $this->address($taxableDeviceLineRep);
                        $taxCode = $taxableDeviceLineRep->getTaxableDeviceLineTaxCode();
                        $taxRate = $this->findTaxRate($taxableDeviceLineRep, $taxCode, $address);
                        $taxLine = $this->calculateTaxLine($taxRate, $taxCode, $address, ...$taxableDeviceLineGroup);
                        $taxLineList->add($taxLine);
                    }
                }
            }
        }

        return $taxLineList;
    }

    /**
     * Calculate tax line.
     */
    protected function calculateTaxLine(TaxRate $taxRate, $taxCode, AddressData $address, TaxableDeviceLine ...$taxableDeviceLines): TaxLine
    {
        $exclusiveTaxAmount = 0;
        $inclusiveTaxAmount = 0;
        foreach ($taxableDeviceLines as $taxableDeviceLine) {
            if ($taxableDeviceLine->isAmountInclusiveOfTax()) {
                $inclusiveTaxAmount += $taxableDeviceLine->getTaxableDeviceLineAmount() * $taxRate->percentage / 100;
            } else {
                $exclusiveTaxAmount += $taxableDeviceLine->getTaxableDeviceLineAmount() * $taxRate->percentage / 100;
            }
        }

        return new TaxLine(
            round($exclusiveTaxAmount),
            round($inclusiveTaxAmount),
            $taxCode,
            $taxRate,
            $address,
            ...$taxableDeviceLines
        );
    }

    /**
     * Validate taxable device lines.
     */
    protected function validateTaxableDeviceLines(array $taxableDeviceLines): void
    {
        foreach ($taxableDeviceLines as $taxableDeviceLine) {
            $this->validateTaxableDeviceLine($taxableDeviceLine);
        }
    }

    /**
     * Validate taxable device line.
     */
    protected function validateTaxableDeviceLine(TaxableDeviceLine $taxableDeviceLine): void
    {
        // Note: no need to check for TaxableDeviceLine type as PHP will already type check arguments.
    }

    /**
     * A helper for retrieving tax rate.
     */
    protected function retrieveTaxRate(string $taxableId, ?string $taxCode, ?string $countryCode, ?string $province, ?string $tenantId): ?TaxRate
    {
        return TaxRate::findSpecial(
            $taxCode,
            $taxableId,
            $countryCode,
            $province,
            $tenantId,
        );
    }

    /**
     * Get the tax rate for a given taxable device line.
     *
     * @throws TaxRateNotFoundException if tax rate not found.
     */
    protected function findTaxRate(TaxableDeviceLine $taxableDeviceLine, string $taxCode, AddressData $address): TaxRate
    {
        $taxableId = $taxableDeviceLine->getTaxable()->getTaxableIdentifier();

        $countryCode = $address->country_code;
        $province = $address->province;

        $taxRate = $this->retrieveTaxRate($taxableId, $taxCode, $countryCode, $province, $this->tenantId);
        if (is_null($taxRate)) {
            $taxCode = is_null($taxCode) ? 'null' : $taxCode;
            $countryCode = is_null($countryCode) ? 'null' : $countryCode;
            $province = is_null($province) ? 'null' : $province;
            $tenantId = is_null($this->tenantId) ? 'null' : $this->tenantId;
            $msg = "Tax rate not found for: tax code: {$taxCode} , 
                                taxable id: {$taxableId},
                                country code: {$countryCode},
                                province: {$province},
                                tenant id: {$tenantId}";
            throw new TaxRateNotFoundException($taxableDeviceLine, $msg);
        }

        return $taxRate;
    }

    /**
     * Set the value of taxableDeviceLineGrouper
     */
    public function setTaxableDeviceLineGrouper($taxableDeviceLineGrouper): static
    {
        $this->taxableDeviceLineGrouper = $taxableDeviceLineGrouper;

        return $this;
    }

    /**
     * Set the value of tenantId
     *
     * @param  ?string  $tenantId
     * @return self
     */
    public function setTenantId(?string $tenantId): static
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    /**
     * Get effective address prioritising shipping address over billing address.
     */
    protected function address(TaxableDeviceLine $taxableDeviceLine): ?AddressData
    {
        return $taxableDeviceLine->getTaxableDeviceLineShippingAddress() ?? $taxableDeviceLine->getTaxableDeviceLineBillingAddress();
    }

    /**
     * Set the value of groupByTaxCode
     */
    public function setGroupByTaxCode(bool $groupByTaxCode): static
    {
        $this->groupByTaxCode = $groupByTaxCode;

        return $this;
    }
}
