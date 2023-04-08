<?php

namespace Autepos\Tax;

use Autepos\Tax\Contracts\TaxableDeviceLine;

/**
 * Group taxable device lines by tax code which is returned by the getTaxableDeviceLineTaxCode() method.
 */
class TaxableDeviceLineGrouper
{
    /**
     * Group taxable device lines by country code.
     *
     * @return array<string, array<int,TaxableDeviceLine>> Array of arrays of taxable device
     * lines grouped by country code.
     */
    public function byCountryCode(TaxableDeviceLine ...$taxableDeviceLines): array
    {
        $groupedTaxableDeviceLines = [];
        foreach ($taxableDeviceLines as $taxableDeviceLine) {
            $address = $taxableDeviceLine->getTaxableDeviceLineShippingAddress() ?? $taxableDeviceLine->getTaxableDeviceLineBillingAddress();
            $countryCode = $address->country_code;
            if (! isset($groupedTaxableDeviceLines[$countryCode])) {
                $groupedTaxableDeviceLines[$countryCode] = [];
            }
            $groupedTaxableDeviceLines[$countryCode][] = $taxableDeviceLine;
        }

        return $groupedTaxableDeviceLines;
    }

    /**
     * Group taxable device lines by province.
     *
     * @return array<string, array<int,TaxableDeviceLine>> Array of arrays of taxable device
     * lines grouped by province. If a taxable device line does not have a province, it will
     * be grouped under the empty string.
     */
    public function byProvince(TaxableDeviceLine ...$taxableDeviceLines): array
    {
        $groupedTaxableDeviceLines = [];
        foreach ($taxableDeviceLines as $taxableDeviceLine) {
            $address = $taxableDeviceLine->getTaxableDeviceLineShippingAddress() ?? $taxableDeviceLine->getTaxableDeviceLineBillingAddress();
            $province = $address->province ?? '';
            if (! isset($groupedTaxableDeviceLines[$province])) {
                $groupedTaxableDeviceLines[$province] = [];
            }
            $groupedTaxableDeviceLines[$province][] = $taxableDeviceLine;
        }

        return $groupedTaxableDeviceLines;
    }

    /**
     * Group taxable device lines by tax code.
     *
     * @return array<string, array<int,TaxableDeviceLine>> Array of arrays of taxable device
     * lines grouped by tax code. If a taxable device line does not have a tax code, it will
     * be grouped under the empty string.
     */
    public function byTaxCode(TaxableDeviceLine ...$taxableDeviceLines): array
    {
        $groupedTaxableDeviceLines = [];
        foreach ($taxableDeviceLines as $taxableDeviceLine) {
            $taxCode = $taxableDeviceLine->getTaxableDeviceLineTaxCode() ?? '';
            if (! isset($groupedTaxableDeviceLines[$taxCode])) {
                $groupedTaxableDeviceLines[$taxCode] = [];
            }
            $groupedTaxableDeviceLines[$taxCode][] = $taxableDeviceLine;
        }

        return $groupedTaxableDeviceLines;
    }
}
