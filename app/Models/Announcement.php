<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'shelter_id',
        'title',
        'body',
    ];

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }
}
