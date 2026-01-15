<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'user_code' => 'USR' . fake()->unique()->numerify('######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => fake()->randomElement([1, 1, 1, 0]),
            'remember_token' => Str::random(10),
            'params' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_code' => 'SV' . fake()->unique()->numerify('#######'),
            'email' => fake()->unique()->regexify('[a-z]{5,10}@utc\.edu\.vn'),
        ]);
    }

    public function lecturer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_code' => 'GV' . fake()->unique()->numerify('######'),
            'email' => fake()->unique()->regexify('[a-z]{5,10}@utc\.edu\.vn'),
        ]);
    }

    public function librarian(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_code' => 'LIB' . fake()->unique()->numerify('###'),
            'email' => fake()->unique()->regexify('[a-z]{5,10}@utc\.edu\.vn'),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_code' => 'ADMIN' . fake()->unique()->numerify('###'),
            'email' => fake()->unique()->regexify('[a-z]{5,10}@utc\.edu\.vn'),
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}
