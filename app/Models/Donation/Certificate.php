<?php

declare(strict_types=1);

namespace App\Models\Donation;

use App\Models\Account\User;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bir bağışa karşılık üretilen teşekkür belgesi. Bağış anındaki
 * bağışçı adı, hayvan adı ve tutar denormalize saklanır — belge sabittir.
 */
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'donation_id',
        'user_id',
        'certificate_no',
        'donor_name',
        'animal_name',
        'amount',
        'issued_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
