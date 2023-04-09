<?php

namespace Autepos\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tax rate model.
 * 
 * @property int $id Id.
 * @property string|null $tenant_id Tenant id.
 * @property string|null $taxable_id Taxable id e.g id of a Product item/shipping cost.
 * @property string|null $tax_code Tax code.
 * @property string|null $country_code Country code.
 * @property string|null $province Province.
 * @property float $percentage Percentage.
 * @property string $tax_type Tax type.
 * @property string $status Status.
 * @property array|null $meta Tax rate meta.
 * @property string|null $description Tax rate description.
 * @property string|null $name Tax rate name.
 * @property \Illuminate\Support\Carbon|null $created_at Date of model creation.
 * @property \Illuminate\Support\Carbon|null $updated_at Date of model update.
 * @property \Autepos\Tax\Models\TaxCode|null $taxCode Tax code.
 * 
 * 
 */
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
     * Relationship with tax code.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
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
