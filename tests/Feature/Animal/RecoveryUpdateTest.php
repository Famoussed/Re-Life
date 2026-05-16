<?php

declare(strict_types=1);

namespace Tests\Feature\Animal;

use App\Enums\Account\Role;
use App\Enums\Shelter\ShelterStatus;
use App\Livewire\Animal\RecoveryUpdateManager;
use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Donation\Donation;
use App\Models\Shelter\Shelter;
use App\Notifications\Notification\RecoveryUpdatePublishedNotification;
use App\Services\Animal\RecoveryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeShelter(string $email, string $license): Shelter
    {
        $admin = User::create([
            'name' => 'Yönetici '.$email,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => Role::Admin->value,
            'email_verified_at' => now(),
        ]);

        return Shelter::create([
            'admin_user_id' => $admin->id,
            'name' => 'Barınak '.$license,
            'license_no' => $license,
            'city' => 'İstanbul',
            'phone' => '0500 000 0000',
            'address' => 'Test adresi',
            'status' => ShelterStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    private function makeAnimal(Shelter $shelter, string $name = 'Pati'): Animal
    {
        return Animal::withoutGlobalScopes()->create([
            'shelter_id' => $shelter->id,
            'name' => $name,
            'species' => 'dog',
            'age_estimate' => '2 yaş',
            'gender' => 'male',
            'story' => 'Test hikâyesi.',
            'health_status' => 'İyileşiyor.',
            'is_active' => true,
        ]);
    }

    private function makeUser(string $email): User
    {
        return User::create([
            'name' => 'Bağışçı '.$email,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => Role::User->value,
            'email_verified_at' => now(),
        ]);
    }

    public function test_publish_creates_update_with_images(): void
    {
        Storage::fake('public');
        Notification::fake();

        $shelter = $this->makeShelter('admin1@test.com', 'LIC-001');
        $animal = $this->makeAnimal($shelter);

        $photos = [
            UploadedFile::fake()->image('foto1.jpg'),
            UploadedFile::fake()->image('foto2.jpg'),
        ];

        $update = app(RecoveryService::class)->publish($animal, [
            'title' => 'Ameliyat başarılı geçti',
            'note' => 'Pati hızla iyileşiyor.',
        ], $photos);

        $this->assertDatabaseHas('recovery_updates', [
            'id' => $update->id,
            'animal_id' => $animal->id,
            'shelter_id' => $shelter->id,
            'title' => 'Ameliyat başarılı geçti',
        ]);

        $this->assertCount(2, $update->images);

        foreach ($update->images as $image) {
            Storage::disk('public')->assertExists($image->image_path);
        }
    }

    public function test_only_donors_of_the_animal_are_notified(): void
    {
        Storage::fake('public');
        Notification::fake();

        $shelter = $this->makeShelter('admin2@test.com', 'LIC-002');
        $animal = $this->makeAnimal($shelter);

        $need = Need::withoutGlobalScopes()->create([
            'animal_id' => $animal->id,
            'shelter_id' => $shelter->id,
            'type' => 'food',
            'title' => 'Mama desteği',
            'target_amount' => 1000,
            'status' => 'active',
        ]);

        // animal_id üzerinden bağış yapan
        $directDonor = $this->makeUser('direct@test.com');
        Donation::create([
            'user_id' => $directDonor->id,
            'shelter_id' => $shelter->id,
            'animal_id' => $animal->id,
            'amount' => 100,
            'currency' => 'TRY',
            'payment_meta' => ['card_last4' => '4242'],
            'created_at' => Carbon::now(),
        ]);

        // need_id üzerinden bağış yapan
        $needDonor = $this->makeUser('need@test.com');
        Donation::create([
            'user_id' => $needDonor->id,
            'shelter_id' => $shelter->id,
            'need_id' => $need->id,
            'amount' => 200,
            'currency' => 'TRY',
            'payment_meta' => ['card_last4' => '4242'],
            'created_at' => Carbon::now(),
        ]);

        // Bağış yapmamış kullanıcı
        $stranger = $this->makeUser('stranger@test.com');

        app(RecoveryService::class)->publish($animal, [
            'title' => 'İyi haber',
            'note' => 'Pati harika.',
        ], [UploadedFile::fake()->image('foto.jpg')]);

        Notification::assertSentTo($directDonor, RecoveryUpdatePublishedNotification::class);
        Notification::assertSentTo($needDonor, RecoveryUpdatePublishedNotification::class);
        Notification::assertNotSentTo($stranger, RecoveryUpdatePublishedNotification::class);
    }

    public function test_admin_can_publish_via_livewire_component(): void
    {
        Storage::fake('public');
        Notification::fake();

        $shelter = $this->makeShelter('admin3@test.com', 'LIC-003');
        $animal = $this->makeAnimal($shelter);

        Livewire::actingAs($shelter->admin)
            ->test(RecoveryUpdateManager::class)
            ->set('animalId', $animal->id)
            ->set('title', 'Livewire üzerinden güncelleme')
            ->set('note', 'Detaylı iyileşme notu.')
            ->set('photos', [UploadedFile::fake()->image('foto.jpg')])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('recovery_updates', [
            'animal_id' => $animal->id,
            'title' => 'Livewire üzerinden güncelleme',
        ]);
    }

    public function test_admin_cannot_publish_for_another_shelters_animal(): void
    {
        Storage::fake('public');
        Notification::fake();

        $shelterA = $this->makeShelter('admina@test.com', 'LIC-A');
        $shelterB = $this->makeShelter('adminb@test.com', 'LIC-B');
        $animalB = $this->makeAnimal($shelterB, 'Boncuk');

        // shelterA admini, shelterB'nin hayvanına güncelleme eklemeye çalışır.
        // ShelterScope nedeniyle hayvan bulunamaz; findOrFail 404'e yol açar.
        $this->expectException(ModelNotFoundException::class);

        try {
            Livewire::actingAs($shelterA->admin)
                ->test(RecoveryUpdateManager::class)
                ->set('animalId', $animalB->id)
                ->set('title', 'İzinsiz güncelleme')
                ->set('note', 'Olmamalı.')
                ->set('photos', [UploadedFile::fake()->image('foto.jpg')])
                ->call('save');
        } finally {
            $this->assertDatabaseMissing('recovery_updates', [
                'animal_id' => $animalB->id,
            ]);
        }
    }

    public function test_validation_requires_at_least_one_photo(): void
    {
        $shelter = $this->makeShelter('admin4@test.com', 'LIC-004');
        $animal = $this->makeAnimal($shelter);

        Livewire::actingAs($shelter->admin)
            ->test(RecoveryUpdateManager::class)
            ->set('animalId', $animal->id)
            ->set('title', 'Fotoğrafsız')
            ->set('note', 'Not var ama foto yok.')
            ->set('photos', [])
            ->call('save')
            ->assertHasErrors(['photos']);
    }
}
