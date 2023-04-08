<?php

namespace Autepos\Tax\Tests\Feature;

use Autepos\Tax\Models\TaxCode;
use Autepos\Tax\Models\TaxRate;
use Autepos\Tax\Tests\Fixtures\TaxableDeviceFixture;
use Autepos\Tax\Tests\Fixtures\TaxableDeviceLineFixture;
use Autepos\Tax\Tests\Fixtures\TaxableFixture;
use Autepos\Tax\Tests\Fixtures\TaxCalculatorFixture;
use Autepos\Tax\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test the TaxCalculator class.
 */
class TaxCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Create default tax rate.
     */
    public function createDefaultTaxRate($percentage = 20)
    {
        $this->createTaxRate(null, null, null, null, null, $percentage);
    }

    /**
     * Create specific tax rate.
     */
    public function createTaxRate(?string $taxableId = null, ?string $taxCode = null, ?string $countryCode = null, ?string $province = null, ?string $tenantId = null, float $percentage = 20)
    {
        TaxCode::factory()->create([
            'code' => $taxCode,
            'name' => 'Service',
        ]);

        TaxRate::factory()->create([
            'taxable_id' => $taxableId,
            'tax_code' => $taxCode,
            'country_code' => $countryCode,
            'province' => $province,
            'tenant_id' => $tenantId,
            'percentage' => $percentage,
        ]);
    }

    /**
     * Test that exception is thrown if no tax rate is found.
     */
    public function testExceptionIsThrownIfNoTaxRateIsFound()
    {
        $this->expectException(\Autepos\Tax\Exceptions\TaxRateNotFoundException::class);

        $taxCode = '100L';
        $shippingProvince = 'CA';
        $shippingCountryCode = 'US';
        $billingProvince = 'London';
        $billingCountryCode = 'GB';

        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(
            1, 'order'
        );

        // Create a taxable device line
        $taxableDeviceLine = new TaxableDeviceLineFixture(1, 'order-line', $taxCode, 100, false);
        $taxableDeviceLine->setShippingAddress(TaxableDeviceLineFixture::makeAddress($shippingCountryCode, $shippingProvince));
        $taxableDeviceLine->setBillingAddress(TaxableDeviceLineFixture::makeAddress($billingCountryCode, $billingProvince));

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine]);

        $taxCalculator->addTaxableDevice($taxableDevice);

        $taxCalculator->calculate();
    }

    /**
     * Test that in general tax can be calculated.
     */
    public function testTaxIsCalculated()
    {
        $taxRatePercentage = 20;
        $taxableId = '123';
        $taxCode = '100L';
        $tenantId = 'tenant-1';

        $shippingCountryCode = 'US';
        $shippingProvince = 'CA';

        $billingCountryCode = 'GB';
        $billingProvince = 'London';

        // Create tax rate
        $this->createTaxRate(
            $taxableId,
            $taxCode,
            $shippingCountryCode,
            $shippingProvince,
            $tenantId,
            $taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();
        $taxCalculator->setTenantId($tenantId);

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a taxable device line
        $taxableDeviceLine = new TaxableDeviceLineFixture(1, 'order-line', $taxCode, 100, false);
        $taxableDeviceLine->setTaxable(new TaxableFixture($taxableId, 'product'));
        $taxableDeviceLine->setShippingAddress(TaxableDeviceLineFixture::makeAddress($shippingCountryCode, $shippingProvince));
        $taxableDeviceLine->setBillingAddress(TaxableDeviceLineFixture::makeAddress($billingCountryCode, $billingProvince));

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Test
        $expected = $taxRatePercentage / 100 * $taxableDeviceLine->getTaxableDeviceLineAmount();
        $this->assertEquals($expected, $taxLineList->totalAmount());
    }

    /**
     * Test that when calculating tax, billing address is used when shipping address is not available.
     */
    public function testBillingAddressIsUsedWhenShippingAddressIsNotAvailable()
    {
        $taxRatePercentage = 20;
        $taxableId = '123';
        $taxCode = '100L';
        $tenantId = 'tenant-1';
        $billingCountryCode = 'GB';
        $billingProvince = 'London';

        // Create tax rate
        $this->createTaxRate(
            $taxableId,
            $taxCode,
            $billingCountryCode,
            $billingProvince,
            $tenantId,
            $taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();
        $taxCalculator->setTenantId($tenantId);

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a taxable device line
        $taxableDeviceLine = new TaxableDeviceLineFixture(1, 'order-line', $taxCode, 100, false);
        $taxableDeviceLine->setTaxable(new TaxableFixture($taxableId, 'product'));
        $taxableDeviceLine->setShippingAddress(null);
        $taxableDeviceLine->setBillingAddress(TaxableDeviceLineFixture::makeAddress($billingCountryCode, $billingProvince));

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Test
        $expected = $taxableDeviceLine->billingAddress;
        $this->assertEquals($expected, $taxLineList->all()[0]->getAddress());
    }

    /**
     * Test that when calculating tax, taxable device lines can be grouped by tax code.
     */
    public function testTaxableDeviceLinesCanBeGroupedByTaxCode()
    {
        $taxCode1 = '100L';
        $taxCode2 = '200L';

        // Create tax rate
        $this->createDefaultTaxRate();

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', $taxCode1, 100, false);
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', $taxCode2, 100, false);

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine1, $taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->setGroupByTaxCode(true)
                                    ->calculate();

        // Test
        $expected = 2;
        $this->assertCount($expected, $taxLineList->all());

        // Test that each tax line has either tax code 1 or 2
        $taxCodeList = [];
        foreach ($taxLineList->all() as $taxLine) {
            $taxCodeList[] = $taxLine->getTaxCode();
        }
        $this->assertContains($taxCode1, $taxCodeList);
        $this->assertContains($taxCode2, $taxCodeList);
    }

    /**
     * Test that when calculating tax, taxable device lines can be grouped by country code.
     */
    public function testTaxableDeviceLinesCanBeGroupedByCountryCode()
    {
        $shippingCountryCode1 = 'US';
        $shippingCountryCode2 = 'GB';

        // Create tax rate
        $this->createDefaultTaxRate();

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, false);
        $taxableDeviceLine1->setShippingAddress(TaxableDeviceLineFixture::makeAddress($shippingCountryCode1, 'CA'));
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', '100L', 100, false);
        $taxableDeviceLine2->setShippingAddress(TaxableDeviceLineFixture::makeAddress($shippingCountryCode2, 'London'));

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine1, $taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Test
        $expected = 2;
        $this->assertCount($expected, $taxLineList->all());

        // Test that each tax line has either tax code 1 or 2
        $countryCodeList = [];
        foreach ($taxLineList->all() as $taxLine) {
            $countryCodeList[] = $taxLine->getAddress()->country_code;
        }
        $this->assertContains($shippingCountryCode1, $countryCodeList);
        $this->assertContains($shippingCountryCode2, $countryCodeList);
    }

    /**
     * Test that when calculating tax, taxable device lines can be grouped by province.
     */
    public function testTaxableDeviceLinesCanBeGroupedByProvince()
    {
        $shippingProvince1 = 'Manchester';
        $shippingProvince2 = 'London';

        // Create tax rate
        $this->createDefaultTaxRate();

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, false);
        $taxableDeviceLine1->setShippingAddress(TaxableDeviceLineFixture::makeAddress('GB', $shippingProvince1));
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', '100L', 100, false);
        $taxableDeviceLine2->setShippingAddress(TaxableDeviceLineFixture::makeAddress('GB', $shippingProvince2));

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine1, $taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Test
        $expected = 2;
        $this->assertCount($expected, $taxLineList->all());

        // Test that each tax line has either tax code 1 or 2
        $provinceList = [];
        foreach ($taxLineList->all() as $taxLine) {
            $provinceList[] = $taxLine->getAddress()->province;
        }
        $this->assertContains($shippingProvince1, $provinceList);
        $this->assertContains($shippingProvince2, $provinceList);
    }

    /**
     * Test that tax can be calculated for more than one taxable device.
     */
    public function testTaxCanBeCalculatedForMoreThanOneTaxableDevice()
    {
        $taxRatePercentage = 20;

        // Create tax rate
        $this->createDefaultTaxRate($taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice1 = new TaxableDeviceFixture(1, 'order');
        $taxableDevice2 = new TaxableDeviceFixture(2, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, false);
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', '100L', 100, false);

        // Add the device line to the device
        $taxableDevice1->setTaxableDeviceLines(...[$taxableDeviceLine1]);
        $taxableDevice2->setTaxableDeviceLines(...[$taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice1);
        $taxCalculator->addTaxableDevice($taxableDevice2);
        $taxLineList = $taxCalculator->calculate();

        // Calculate expected tax
        $expected = ($taxRatePercentage / 100) * (100 + 100);

        // Test
        $this->assertEquals($expected, $taxLineList->totalAmount());
    }

    /**
     * Test that tax can be calculated for more than one taxable device lines.
     */
    public function testTaxCanBeCalculatedForMoreThanOneTaxableDeviceLine()
    {
        $taxRatePercentage = 20;

        // Create tax rate
        $this->createDefaultTaxRate($taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, false);
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', '100L', 100, false);

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine1, $taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Calculate expected tax
        $expected = ($taxRatePercentage / 100) * (100 + 100);

        // Test
        $this->assertEquals($expected, $taxLineList->totalAmount());
    }

    /**
     * Test that tax can be calculated for a taxable device line with inclusive tax.
     */
    public function testTaxCanBeCalculatedForTaxableDeviceLineWithInclusiveTax()
    {
        $taxRatePercentage = 20;

        // Create tax rate
        $this->createDefaultTaxRate($taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, true);

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Calculate expected tax
        $expected = ($taxRatePercentage / 100) * (100);

        // Test
        $this->assertEquals($expected, $taxLineList->totalAmount());
        $this->assertEquals($expected, $taxLineList->inclusiveAmount());
        $this->assertEquals(0, $taxLineList->exclusiveAmount());
    }

    /**
     * Test that tax can be calculated for taxable device lines with some inclusive and others exclusive tax.
     */
    public function testTaxCanBeCalculatedForTaxableDeviceLinesWithSomeInclusiveAndOthersExclusiveTax()
    {
        $taxRatePercentage = 20;

        // Create tax rate
        $this->createDefaultTaxRate($taxRatePercentage);

        // Create tax calculator
        $taxCalculator = new TaxCalculatorFixture();

        // Create taxable device
        $taxableDevice = new TaxableDeviceFixture(1, 'order');

        // Create a couple of taxable device lines.
        $taxableDeviceLine1 = new TaxableDeviceLineFixture(1, 'order-line', '100L', 100, true);
        $taxableDeviceLine2 = new TaxableDeviceLineFixture(2, 'order-line', '100L', 500, false);

        // Add the device line to the device
        $taxableDevice->setTaxableDeviceLines(...[$taxableDeviceLine1, $taxableDeviceLine2]);

        // Add the device to the tax calculator and calculate tax
        $taxCalculator->addTaxableDevice($taxableDevice);
        $taxLineList = $taxCalculator->calculate();

        // Calculate expected tax
        $expected = ($taxRatePercentage / 100) * (100 + 500);
        $expectedInclusive = ($taxRatePercentage / 100) * (100);
        $expectedExclusive = ($taxRatePercentage / 100) * (500);

        // Test
        $this->assertEquals($expected, $taxLineList->totalAmount());
        $this->assertEquals($expectedInclusive, $taxLineList->inclusiveAmount());
        $this->assertEquals($expectedExclusive, $taxLineList->exclusiveAmount());
    }
}
