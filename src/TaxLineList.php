<?php

namespace Autepos\Tax;

class TaxLineList
{
    /**
     * Tax lines.
     *
     * @var array<int,TaxLine>
     */
    protected $taxLines = [];

    /**
     * TaxLineList constructor.
     */
    public function __construct(array $taxLines = [])
    {
        $this->taxLines = $taxLines;
    }

    /**
     * Add a tax line.
     */
    public function add(TaxLine $taxLine)
    {
        $this->taxLines[] = $taxLine;
    }

    /**
     * Get tax lines.
     *
     * @return array<int,TaxLine>
     */
    public function all(): array
    {
        return $this->taxLines;
    }

    /**
     * Get tax amount already included in price. This amount should not be
     * be added to order subtotal.
     */
    public function inclusiveAmount(): int
    {
        $inclusiveAmount = 0;

        foreach ($this->taxLines as $taxLine) {
            $inclusiveAmount += $taxLine->getInclusiveAmount();
        }

        return $inclusiveAmount;
    }

    /**
     * Get tax amount not included in price. This amount needs to be added
     * to order subtotal.
     */
    public function exclusiveAmount(): int
    {
        $exclusiveAmount = 0;

        foreach ($this->taxLines as $taxLine) {
            $exclusiveAmount += $taxLine->getExclusiveAmount();
        }

        return $exclusiveAmount;
    }

    /**
     * Get the total of inclusive + exclusive amount. This amount should
     * not be added to order subtotal. It should only be used to display
     * the total tax amount.
     */
    public function totalAmount(): int
    {
        $totalAmount = 0;

        foreach ($this->taxLines as $taxLine) {
            $totalAmount += $taxLine->totalAmount();
        }

        return $totalAmount;
    }

    /**
     * Get tax lines as an array.
     */
    public function toArray(): array
    {
        $taxLines = [];

        foreach ($this->taxLines as $taxLine) {
            $taxLines[] = $taxLine->toArray();
        }

        return $taxLines;
    }
}
