<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Animal\RecoveryUpdateImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecoveryUpdateImage>
 */
class RecoveryUpdateImageFactory extends Factory
{
    protected $model = RecoveryUpdateImage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => 'recovery-updates/'.$this->faker->uuid().'.jpg',
            'sort_order' => 0,
        ];
    }
}
