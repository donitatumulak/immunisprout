<?php

namespace Database\Factories;

use App\Models\HealthWorker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthWorker>
 */
class HealthWorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = HealthWorker::class;
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'wrk_first_name' => $faker->firstName(),
            'wrk_last_name' => $faker->lastName(),
            'wrk_contact_number' => $faker->phoneNumber(),
            'wrk_addr_id' => \App\Models\Address::factory(),
            'wrk_role' => $faker->randomElement(['nurse', 'midwife', 'bhw', 'admin']),
        ];
    }
}
