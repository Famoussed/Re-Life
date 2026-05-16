<?php

declare(strict_types=1);

namespace App\Actions\Animal;

use App\Models\Animal\Animal;
use App\Models\Animal\RecoveryUpdate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Tek bir iyileşme güncellemesi kaydı oluşturur ve yüklenen fotoğrafları
 * herkese açık diske kaydederek görsel satırlarını ekler. Transaction açmaz.
 */
class CreateRecoveryUpdateAction
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, TemporaryUploadedFile>  $photos
     */
    public function execute(Animal $animal, array $data, array $photos): RecoveryUpdate
    {
        $update = RecoveryUpdate::create([
            'animal_id' => $animal->id,
            'shelter_id' => $animal->shelter_id,
            'title' => $data['title'],
            'note' => $data['note'],
        ]);

        foreach (array_values($photos) as $index => $photo) {
            $path = $photo->store('recovery-updates', 'public');

            $update->images()->create([
                'image_path' => $path,
                'sort_order' => $index,
            ]);
        }

        return $update;
    }
}
