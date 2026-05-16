<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Donation\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'certificate_no' => 'RL-'.now()->year.'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'donor_name' => $this->faker->name(),
            'animal_name' => $this->faker->firstName(),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'issued_at' => now(),
        ];
    }
}
