<?php

declare(strict_types=1);

namespace App\Services\Animal;

use App\Actions\Animal\CreateRecoveryUpdateAction;
use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\RecoveryUpdate;
use App\Models\Donation\Donation;
use App\Notifications\Notification\RecoveryUpdatePublishedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * İyileşme güncellemesi yayınlar ve hayvana bağış yapan kullanıcıları bildirir.
 */
class RecoveryService
{
    public function __construct(private CreateRecoveryUpdateAction $createAction) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, TemporaryUploadedFile>  $photos
     */
    public function publish(Animal $animal, array $data, array $photos): RecoveryUpdate
    {
        return DB::transaction(function () use ($animal, $data, $photos) {
            $update = $this->createAction->execute($animal, $data, $photos);

            // Hayvanın ihtiyaç kimlikleri — global scope dışı (admin filtresine takılmasın).
            $needIds = $animal->needs()->withoutGlobalScopes()->pluck('id');

            // O hayvana doğrudan ya da ihtiyaçları üzerinden bağış yapanlar.
            $donorIds = Donation::query()
                ->where(function ($query) use ($animal, $needIds) {
                    $query->where('animal_id', $animal->id)
                        ->orWhereIn('need_id', $needIds);
                })
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');

            $donors = User::whereIn('id', $donorIds)->get();

            Notification::send($donors, new RecoveryUpdatePublishedNotification($update));

            return $update;
        });
    }
}
