<?php

namespace Tests\Unit;

use Autepos\Tax\Models\TaxCode;
use Autepos\Tax\Models\TaxRate;
use Autepos\Tax\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaxRateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that specific tax rate has priority.
     *
     * @return void
     */
    public function testSpecificTaxRateHasPriority()
    {
        // Insert data for tax code and tax rate
        TaxCode::factory()->create([
            'code' => '100L',
            'name' => 'Service',
        ]);

        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Specific tax rate
        $taxRate = TaxRate::factory()->create([
            'tenant_id' => '123',
            'tax_code' => '100L',
            'country_code' => 'US',
            'province' => 'CA',
            'taxable_id' => '1',
            'percentage' => 10,
        ]);

        $result = TaxRate::findSpecial('100L', '1', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that an inactive tax rate is not returned.
     *
     * @return void
     */
    public function testInactiveTaxRateIsNotReturned()
    {
        TaxRate::factory()->create([
            'status' => TaxRate::STATUS_INACTIVE,
        ]);

        $result = TaxRate::findSpecial();

        $this->assertNull($result);
    }

    /**
     * Test that a tax rate is returned when all parameters are null.
     *
     * @return void
     */
    public function testReturnsTaxRateWhenAllParametersAreNull()
    {
        $taxRate = TaxRate::factory()->default()->create();

        $result = TaxRate::findSpecial();
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate is returned when all parameters match exactly.
     *
     * @return void
     */
    public function testReturnsTaxRateWhenAllParametersMatch()
    {
        // Insert nuisance data
        TaxRate::factory()->default()->create();

        // Insert data for tax code and tax rate
        TaxCode::factory()->create([
            'code' => '100L',
            'name' => 'Service',
        ]);
        $taxRate = TaxRate::factory()->create([
            'tax_code' => '100L',
            'taxable_id' => '123p',
            'country_code' => 'US',
            'province' => 'CA',
            'tenant_id' => 't123',
        ]);

        // Test
        $result = TaxRate::findSpecial('100L', '123p', 'US', 'CA', 't123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate is returned when some parameters match and some are null.
     *
     * @return void
     */
    public function testReturnsTaxRateWhenSomeParametersMatchAndSomeAreNull()
    {
        // Insert nuisance data
        TaxRate::factory()->default()->create();

        // Insert data for tax code and tax rate
        TaxCode::factory()->create([
            'code' => '100L',
            'name' => 'Service',
        ]);
        $taxRate = TaxRate::factory()->create([
            'tax_code' => '100L',
            'taxable_id' => null,
            'country_code' => 'US',
            'province' => 'CA',
            'tenant_id' => null,
        ]);

        // Test
        $result = TaxRate::findSpecial('100L', null, 'US', 'CA', null);
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test default tax rate is returned when no matching tax rate is found.
     *
     * @return void
     */
    public function testDefaultTaxRateWhenNoMatchingTaxRateIsFound()
    {
        // Default tax rate
        $taxRate = TaxRate::factory()->default()->create();

        // Insert specific and nuisance data for tax code and tax rate
        TaxCode::factory()->create([
            'code' => '100L',
            'name' => 'Service',
        ]);
        TaxRate::factory()->create([
            'tax_code' => '100L',
            'taxable_id' => '123',
            'country_code' => 'US',
            'province' => 'CA',
            'tenant_id' => '123',
        ]);

        // Test
        $result = TaxRate::findSpecial('missing', 'missing', 'missing', 'missing', 'missing');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate specific for a tenant id is prioritized.
     *
     * @return void
     */
    public function testTenantSpecificTaxRateIsPrioritized()
    {
        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Tenant specific tax rate
        $taxRate = TaxRate::factory()->default()->create([
            'tenant_id' => '123',
        ]);

        $result = TaxRate::findSpecial('100L', '1', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate specific for a product id is prioritized.
     *
     * @return void
     */
    public function testProductSpecificTaxRateIsPrioritized()
    {
        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Product specific tax rate
        $taxRate = TaxRate::factory()->default()->create([
            'taxable_id' => '123',
        ]);

        $result = TaxRate::findSpecial('100L', '123', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate specific for a country code is prioritized.
     *
     * @return void
     */
    public function testCountrySpecificTaxRateIsPrioritized()
    {
        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Country specific tax rate
        $taxRate = TaxRate::factory()->default()->create([
            'country_code' => 'US',
        ]);

        $result = TaxRate::findSpecial('100L', '1', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate specific for a province is prioritized.
     *
     * @return void
     */
    public function testProvinceSpecificTaxRateIsPrioritized()
    {
        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Province specific tax rate
        $taxRate = TaxRate::factory()->default()->create([
            'province' => 'CA',
        ]);

        $result = TaxRate::findSpecial('100L', '1', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }

    /**
     * Test that a tax rate specific for a tax code is prioritized.
     *
     * @return void
     */
    public function testTaxCodeSpecificTaxRateIsPrioritized()
    {
        // Insert specific tax code
        TaxCode::factory()->create([
            'code' => '100L',
            'name' => 'Service',
        ]);

        // Non-specific tax rate
        TaxRate::factory()->default()->create();

        // Tax code specific tax rate
        $taxRate = TaxRate::factory()->default()->create([
            'tax_code' => '100L',
        ]);

        $result = TaxRate::findSpecial('100L', '1', 'US', 'CA', '123');
        $this->assertNotNull($result);
        $this->assertEquals($taxRate->id, $result->id);
    }
}
