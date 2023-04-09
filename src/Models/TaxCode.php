<?php

namespace Autepos\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tax code model.
 * 
 * @property int $id Id.
 * @property string|null $tenant_id Tenant id.
 * @property string|null $code Tax code.
 * @property string $name Tax code name.
 * @property string|null $description Tax code description.
 * @property array|null $meta Tax code meta.
 * @property \Illuminate\Support\Carbon|null $created_at Date of model creation.
 * @property \Illuminate\Support\Carbon|null $updated_at Date of model update.
 * @property \Illuminate\Database\Eloquent\Collection|\Autepos\Tax\Models\TaxRate[] $taxRates Tax rates.
 * 
 */
class TaxCode extends Model
{
    use HasFactory;

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
        return \Autepos\Tax\Database\Factories\TaxCodeFactory::new();
    }

    /**
     * Relationship with tax rates.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxRates()
    {
        return $this->hasMany(TaxRate::class);
    } 

}
