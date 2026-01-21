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

        $faker = \Faker\Factory::create();
        return [
            'chd_first_name' => $faker->firstName(),
            'chd_middle_name' => $faker->lastName(), 
            'chd_last_name' => $faker->lastName(),
            'chd_date_of_birth' => $faker->dateTimeBetween('2025-01-01', '2025-12-31')->format('Y-m-d'),
            'chd_sex' => $faker->randomElement(['male', 'female']),
            'chd_residency_status' => $faker->randomElement(['established resident', 'transitional resident']),
            'chd_status' => $faker->randomElement(['active', 'inactive', 'complete', 'transferred']),
            
            'chd_current_addr_id' => null, 
            'chd_guardian_id' => null,
        ];
    }
}