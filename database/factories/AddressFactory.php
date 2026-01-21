<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Address::class;
    public function definition(): array
    {
        return [
            'addr_line_1' => fake()->streetAddress(),
            'addr_barangay' => 'Pusok',
            'addr_city_municipality' => 'Lapu-Lapu City',
            'addr_province' => 'Cebu',
            'addr_zip_code' => '6015',
        ];
    }
}
