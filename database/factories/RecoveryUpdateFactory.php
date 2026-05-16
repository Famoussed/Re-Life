<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Animal\RecoveryUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecoveryUpdate>
 */
class RecoveryUpdateFactory extends Factory
{
    protected $model = RecoveryUpdate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'note' => $this->faker->paragraph(),
        ];
    }
}
