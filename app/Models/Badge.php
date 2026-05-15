<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'level',
        'name',
        'min_amount',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'min_amount' => 'decimal:2',
        ];
    }
}
