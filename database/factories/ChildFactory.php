<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildFactory extends Factory
{
    protected $model = Child::class;

    public function definition(): array
    {

        return [
            'chd_first_name' => $this->faker->firstName(),
            'chd_middle_name' => $this->faker->lastName(), 
            'chd_last_name' => $this->faker->lastName(),
            'chd_date_of_birth' => $this->faker->dateTimeBetween('2025-01-01', '2025-12-31')->format('Y-m-d'),
            'chd_sex' => $this->faker->randomElement(['male', 'female']),
            'chd_residency_status' => $this->faker->randomElement(['established resident', 'transitional resident']),
            'chd_status' => $this->faker->randomElement(['active', 'inactive', 'completed', 'transferred']),
            
            'chd_current_addr_id' => null, 
            'chd_guardian_id' => null,
        ];
    }
}