<?php

namespace Autepos\Tax\Exceptions;

use Autepos\Tax\Contracts\TaxableDeviceLine;

/**
 * Tax rate not found exception.
 */
class TaxRateNotFoundException extends \Exception implements ExceptionInterface
{
    /**
     * Taxable device line.
     */
    protected TaxableDeviceLine $taxableDeviceLine;

    /**
     * Constructor.
     *
     * @param  \Throwable  $previous
     */
    public function __construct(TaxableDeviceLine $taxableDeviceLine, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->taxableDeviceLine = $taxableDeviceLine;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the value of taxableDeviceLine
     */
    public function getTaxableDeviceLine(): TaxableDeviceLine
    {
        return $this->taxableDeviceLine;
    }
}
