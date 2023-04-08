<?php

namespace Autepos\Tax\Database\Factories;

use Autepos\Tax\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxRateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaxRate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tenant_id' => $this->faker->uuid,
            'tax_code' => null,
            'country_code' => $this->faker->countryCode,
            'province' => $this->faker->state,
            'taxable_id' => $this->faker->numberBetween(1, 100),
            'percentage' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->randomElement([TaxRate::STATUS_ACTIVE]),
            'meta' => null,
            'description' => '',
            'name' => $this->faker->unique()->words(3, true),
        ];
    }

    /**
     * Default tax rate.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function default()
    {
        return $this->state(function (array $attributes) {
            return [
                'tenant_id' => null,
                'tax_code' => null,
                'country_code' => null,
                'province' => null,
                'taxable_id' => null,
                'percentage' => 20,
                'status' => TaxRate::STATUS_ACTIVE,
                'description' => 'Default tax rate',
            ];
        });
    }
}
