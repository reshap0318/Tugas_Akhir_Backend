<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    
    protected $model = User::class;

    public function definition()
    {
        return [
            'id'        => $this->faker->unique()->numberBetween($min = 1000000000, $max = 9999999999),
            'name'      => $this->faker->name,
            'email'     => $this->faker->unique()->safeEmail,
            'role'      => 1,
            'password'  => Hash::make('12345678')
        ];
    }
}
