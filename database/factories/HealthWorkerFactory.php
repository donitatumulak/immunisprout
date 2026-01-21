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
        return [
            'wrk_first_name' => $this->faker->firstName(),
            'wrk_last_name' => $this->faker->lastName(),
            'wrk_contact_number' => $this->faker->phoneNumber(),
            'wrk_addr_id' => \App\Models\Address::factory(),
            'wrk_role' => $this->faker->randomElement(['nurse', 'midwife', 'bhw', 'admin']),
        ];
    }
}
