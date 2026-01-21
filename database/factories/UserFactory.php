<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\HealthWorker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'worker_id' => HealthWorker::factory(), 
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'), 
            'remember_token' => Str::random(10),
            'last_login' => null,
            'user_status' => 'active',
        ];
    }
}