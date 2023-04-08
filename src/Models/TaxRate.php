<?php

namespace Autepos\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    /**
     * Status string for active
     */
    public const STATUS_ACTIVE = 'active';

    /**
     * Status string for inactive
     */
    public const STATUS_INACTIVE = 'inactive';

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * {@inheritDoc}
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Autepos\Tax\Database\Factories\TaxRateFactory::new();
    }

    /**
     * Given a tax code, perform a query to find a suitable tax rate.
     * Always search for a tax rate with null tax code in case the given tax
     * code is not found or is null. Also always search for a tax rate
     * with null product id in case the given product id is not found or is
     * null. Also, always search for a tax rate with null
     * country code in case the given country code is not found or is null.
     * Also always search for a tax rate with null province in case the given
     * province is not found or is null. Also, always search for a tax rate
     * with null tenant id in case the given tenant id is not found or is null.
     *
     * Order the result so that rows with null tax code, null country code,
     * null province and null product id are returned last.
     *
     * @return \Autepos\Tax\Models\TaxRate|null
     */
    public static function findSpecial(
        ?string $taxCode = null,
        ?string $taxableId = null,
        ?string $countryCode = null,
        ?string $province = null,
        ?string $tenantId = null
    ): ?TaxRate {
        $query = static::query();

        $query->where(function ($query) use ($taxCode) {
            $query->where('tax_code', $taxCode)
                ->orWhereNull('tax_code');
        });

        $query->where(function ($query) use ($taxableId) {
            $query->where('taxable_id', $taxableId)
                ->orWhereNull('taxable_id');
        });

        $query->where(function ($query) use ($countryCode) {
            $query->where('country_code', $countryCode)
                ->orWhereNull('country_code');
        });

        $query->where(function ($query) use ($province) {
            $query->where('province', $province)
                ->orWhereNull('province');
        });

        $query->where(function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)
                ->orWhereNull('tenant_id');
        });

        $query->where('status', static::STATUS_ACTIVE);

        // Make sure that rows with null tax code, null country code,
        // null province and null product id are returned last. But
        $query->orderByRaw('tax_code IS NULL');
        $query->orderByRaw('taxable_id IS NULL');
        $query->orderByRaw('country_code IS NULL');
        $query->orderByRaw('province IS NULL');
        $query->orderByRaw('tenant_id IS NULL');

        return $query->first();
    }
}
