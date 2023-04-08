<?php

namespace Autepos\Tax\Tests\Feature;

use Autepos\Tax\Contracts\TaxableDeviceLine;
use Autepos\Tax\TaxableDeviceLineGrouper;
use Autepos\Tax\Tests\Fixtures\TaxableDeviceLineFixture;
use Autepos\Tax\Tests\TestCase;

/**
 * Test the TaxableDeviceLineGrouper class.
 */
class TaxableDeviceLineGrouperTest extends TestCase
{
    /**
     * Test the byTaxCode() method.
     */
    public function testByTaxCode()
    {
        $taxableDeviceLine1 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine1->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode1');
        $taxableDeviceLine2 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine2->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode2');
        $taxableDeviceLine3 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine3->method('getTaxableDeviceLineTaxCode')->willReturn(null);
        $taxableDeviceLine4 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine4->method('getTaxableDeviceLineTaxCode')->willReturn(null);
        $taxableDeviceLine5 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine5->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode1');
        $taxableDeviceLine6 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine6->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode2');
        $taxableDeviceLine7 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine7->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode1');
        $taxableDeviceLine8 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine8->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode2');
        $taxableDeviceLine9 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine9->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode1');
        $taxableDeviceLine10 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine10->method('getTaxableDeviceLineTaxCode')->willReturn('taxCode2');
        $taxableDeviceLine11 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine11->method('getTaxableDeviceLineTaxCode')->willReturn(null);

        $taxableDeviceLineGrouper = new TaxableDeviceLineGrouper();
        $groupedTaxableDeviceLines = $taxableDeviceLineGrouper->byTaxCode(
            $taxableDeviceLine1,
            $taxableDeviceLine2,
            $taxableDeviceLine3,
            $taxableDeviceLine4,
            $taxableDeviceLine5,
            $taxableDeviceLine6,
            $taxableDeviceLine7,
            $taxableDeviceLine8,
            $taxableDeviceLine9,
            $taxableDeviceLine10,
            $taxableDeviceLine11
        );

        $this->assertEquals([
            'taxCode1' => [
                $taxableDeviceLine1,
                $taxableDeviceLine5,
                $taxableDeviceLine7,
                $taxableDeviceLine9,
            ],
            'taxCode2' => [
                $taxableDeviceLine2,
                $taxableDeviceLine6,
                $taxableDeviceLine8,
                $taxableDeviceLine10,
            ],
            '' => [
                $taxableDeviceLine3,
                $taxableDeviceLine4,
                $taxableDeviceLine11,
            ],
        ], $groupedTaxableDeviceLines);
    }

    /**
     * Test the byCountryCode() method.
     */
    public function testByCountryCode()
    {
        $address1 = TaxableDeviceLineFixture::makeAddress('countryCode1', 'province1');
        $address2 = TaxableDeviceLineFixture::makeAddress('countryCode2', 'province2');

        $taxableDeviceLine1 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine1->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine2 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine2->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine3 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine3->method('getTaxableDeviceLineShippingAddress')->willReturn(null);
        $taxableDeviceLine4 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine4->method('getTaxableDeviceLineShippingAddress')->willReturn(null);
        $taxableDeviceLine5 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine5->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine6 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine6->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine7 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine7->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine8 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine8->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine9 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine9->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine10 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine10->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);

        $taxableDeviceLineGrouper = new TaxableDeviceLineGrouper();
        $groupedTaxableDeviceLines = $taxableDeviceLineGrouper->byCountryCode(
            $taxableDeviceLine1,
            $taxableDeviceLine2,
            $taxableDeviceLine3,
            $taxableDeviceLine4,
            $taxableDeviceLine5,
            $taxableDeviceLine6,
            $taxableDeviceLine7,
            $taxableDeviceLine8,
            $taxableDeviceLine9,
            $taxableDeviceLine10
        );

        $this->assertEquals([
            'countryCode1' => [
                $taxableDeviceLine1,
                $taxableDeviceLine5,
                $taxableDeviceLine7,
                $taxableDeviceLine9,
            ],
            'countryCode2' => [
                $taxableDeviceLine2,
                $taxableDeviceLine6,
                $taxableDeviceLine8,
                $taxableDeviceLine10,
            ],
            '' => [
                $taxableDeviceLine3,
                $taxableDeviceLine4,
            ],
        ], $groupedTaxableDeviceLines);
    }

    /**
     * Test the byProvince() method.
     */
    public function testByProvince()
    {
        $address1 = TaxableDeviceLineFixture::makeAddress('countryCode1', 'province1');
        $address2 = TaxableDeviceLineFixture::makeAddress('countryCode2', 'province2');

        $taxableDeviceLine1 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine1->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine2 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine2->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine3 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine3->method('getTaxableDeviceLineShippingAddress')->willReturn(null);
        $taxableDeviceLine4 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine4->method('getTaxableDeviceLineShippingAddress')->willReturn(null);
        $taxableDeviceLine5 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine5->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine6 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine6->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine7 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine7->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine8 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine8->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);
        $taxableDeviceLine9 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine9->method('getTaxableDeviceLineShippingAddress')->willReturn($address1);
        $taxableDeviceLine10 = $this->createMock(TaxableDeviceLine::class);
        $taxableDeviceLine10->method('getTaxableDeviceLineShippingAddress')->willReturn($address2);

        $taxableDeviceLineGrouper = new TaxableDeviceLineGrouper();
        $groupedTaxableDeviceLines = $taxableDeviceLineGrouper->byProvince(
            $taxableDeviceLine1,
            $taxableDeviceLine2,
            $taxableDeviceLine3,
            $taxableDeviceLine4,
            $taxableDeviceLine5,
            $taxableDeviceLine6,
            $taxableDeviceLine7,
            $taxableDeviceLine8,
            $taxableDeviceLine9,
            $taxableDeviceLine10
        );

        $this->assertEquals([
            'province1' => [
                $taxableDeviceLine1,
                $taxableDeviceLine5,
                $taxableDeviceLine7,
                $taxableDeviceLine9,
            ],
            'province2' => [
                $taxableDeviceLine2,
                $taxableDeviceLine6,
                $taxableDeviceLine8,
                $taxableDeviceLine10,
            ],
            '' => [
                $taxableDeviceLine3,
                $taxableDeviceLine4,
            ],
        ], $groupedTaxableDeviceLines);
    }
}
