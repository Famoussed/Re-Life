<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'total_donated',
        'badge_level',
        'is_banned',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'total_donated' => 'decimal:2',
            'badge_level' => 'integer',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Bu kullanıcının yönettiği barınak (yalnızca admin için).
     */
    public function shelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'admin_user_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function isSuperadmin(): bool
    {
        return $this->role === Role::Superadmin;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Bu kullanıcının kazandığı en güncel rozet tanımı.
     */
    public function badge(): ?Badge
    {
        return $this->badge_level > 0
            ? Badge::where('level', $this->badge_level)->first()
            : null;
    }
}
