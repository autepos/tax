<?php

namespace Autepos\Tax\Tests\Feature;

use Autepos\Tax\TaxManager;
use Autepos\Tax\Tests\TestCase;

class TaxManagerTest extends TestCase
{
    // Test that the tax manager can be instantiated.
    public function testTaxManagerCanBeInstantiated()
    {
        $this->assertInstanceOf(TaxManager::class, $this->taxManager());
    }
}
