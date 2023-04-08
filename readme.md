# Introduction
This package is a tax calculator for Laravel for those who like it interface.  


# Requirements
- PHP 8.0+
- Laravel 9.x+

# Installation
Install the package via composer:
```bash
composer require autepos/tax

php artisan migrate
```

# Simple usage
```php
use Autepos\Tax\Contracts\TaxableDevice;
use Autepos\Tax\Contracts\TaxCalculatorFactory;

class Order implements TaxableDevice{
    //... Implementation of certain methods require other interfaces to be implemented
}

$order = new Order();
$taxCalculator = app(TaxCalculatorFactory::class);
$taxCalculator->addTaxableDevice($order);
$taxLineList=$taxCalculator->calculate();

$totalTax=$taxLineList->totalAmount();// To be displayed as a total tax
$exclusiveTax=$taxLineList->exclusiveAmount();// To be added to the order subtotal
$inclusiveTax=$taxLineList->inclusiveAmount(); // Already included in the order subtotal
```









