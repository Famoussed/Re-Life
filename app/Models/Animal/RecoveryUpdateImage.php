<?php

declare(strict_types=1);

namespace App\Models\Animal;

use Database\Factories\RecoveryUpdateImageFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Bir iyileşme güncellemesine ait tek bir fotoğraf.
 */
class RecoveryUpdateImage extends Model
{
    /** @use HasFactory<RecoveryUpdateImageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'recovery_update_id',
        'image_path',
        'sort_order',
    ];

    public function recoveryUpdate(): BelongsTo
    {
        return $this->belongsTo(RecoveryUpdate::class);
    }

    /**
     * Fotoğrafın herkese açık URL'i. Tam bir URL saklanmışsa (örn. tohum verisi)
     * olduğu gibi döner; aksi halde depolama yolundan üretilir.
     *
     * @return Attribute<string, never>
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : Storage::url($this->image_path));
    }
}
