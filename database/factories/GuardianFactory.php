<?php

namespace Database\Factories;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Guardian::class;
    public function definition(): array
    {
        return [
            'grd_first_name' => fake()->firstName(),
            'grd_last_name' => fake()->lastName(),
            'grd_contact_number' => fake()->phoneNumber(),
            'grd_relationship' => fake()->randomElement(['mother', 'father', 'grandparent', 'legal guardian', 'other']),
            'grd_current_addr_id' => \App\Models\Address::factory(),
        ];
    }
}
