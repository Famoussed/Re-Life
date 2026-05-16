<?php

declare(strict_types=1);

namespace App\Models\Donation;

use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Shelter\Shelter;
use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'shelter_id',
        'animal_id',
        'need_id',
        'amount',
        'currency',
        'is_anonymous',
        'payment_meta',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'payment_meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScopes();
    }

    public function need(): BelongsTo
    {
        return $this->belongsTo(Need::class)->withoutGlobalScopes();
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
