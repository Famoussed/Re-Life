<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShelterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelter extends Model
{
    /** @use HasFactory<\Database\Factories\ShelterFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'admin_user_id',
        'name',
        'license_no',
        'city',
        'phone',
        'address',
        'status',
        'approved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ShelterStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function isApproved(): bool
    {
        return $this->status === ShelterStatus::Approved;
    }
}
