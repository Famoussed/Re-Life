<?php

declare(strict_types=1);

namespace App\Models\Animal;

use App\Models\Shelter\Shelter;
use App\Scopes\Shelter\ShelterScope;
use Database\Factories\RecoveryUpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bir hayvanın iyileşme günlüğündeki tek bir kayıt: başlık, not ve fotoğraflar.
 */
class RecoveryUpdate extends Model
{
    /** @use HasFactory<RecoveryUpdateFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'animal_id',
        'shelter_id',
        'title',
        'note',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ShelterScope);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RecoveryUpdateImage::class)->orderBy('sort_order');
    }
}
