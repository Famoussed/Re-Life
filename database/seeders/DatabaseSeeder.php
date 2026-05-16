<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Donation\CreateCertificateAction;
use App\Enums\Account\Role;
use App\Enums\Shelter\ShelterStatus;
use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Animal\RecoveryUpdate;
use App\Models\Donation\Badge;
use App\Models\Donation\Donation;
use App\Models\Shelter\Shelter;
use App\Notifications\Notification\RecoveryUpdatePublishedNotification;
use App\Services\Donation\DonationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBadges();

        // Platform yöneticisi
        User::create([
            'name' => 'Platform Yöneticisi',
            'email' => 'superadmin@relife.test',
            'password' => Hash::make('password'),
            'role' => Role::Superadmin->value,
            'email_verified_at' => now(),
        ]);

        // Onaylı barınaklar
        $shelters = [
            $this->makeShelter('Patiköy Hayvan Barınağı', 'patikoy@relife.test', 'İstanbul', 'PK-34-0091'),
            $this->makeShelter('Umut Yuvası', 'umut@relife.test', 'İzmir', 'UY-35-0142'),
            $this->makeShelter('Sıcak Burun Barınağı', 'sicakburun@relife.test', 'Ankara', 'SB-06-0277'),
        ];

        // Onay bekleyen barınak
        $this->makePendingShelter('Yeni Umutlar Barınağı', 'yeniumutlar@relife.test', 'Bursa', 'YU-16-0033');

        $this->seedAnimals($shelters);

        // Bağışçılar
        $donors = [
            User::create(['name' => 'Selin Demir', 'email' => 'selin@relife.test', 'password' => Hash::make('password'), 'role' => Role::User->value, 'email_verified_at' => now()]),
            User::create(['name' => 'Ahmet Kaya', 'email' => 'ahmet@relife.test', 'password' => Hash::make('password'), 'role' => Role::User->value, 'email_verified_at' => now()]),
            User::create(['name' => 'Zeynep Yıldız', 'email' => 'zeynep@relife.test', 'password' => Hash::make('password'), 'role' => Role::User->value, 'email_verified_at' => now()]),
            User::create(['name' => 'Mert Aydın', 'email' => 'mert@relife.test', 'password' => Hash::make('password'), 'role' => Role::User->value, 'email_verified_at' => now()]),
        ];

        $this->seedDonations($donors);
        $this->seedRecoveryUpdates();
        $this->seedCertificates();
    }

    private function seedBadges(): void
    {
        $badges = [
            [1, 'Bronz Patiseven', 50],
            [2, 'Gümüş Koruyucu', 500],
            [3, 'Altın Hami', 5000],
            [4, 'Platin Yardımcı', 25000],
            [5, 'Elmas Şampiyon', 200000],
        ];

        foreach ($badges as [$level, $name, $min]) {
            Badge::create(['level' => $level, 'name' => $name, 'min_amount' => $min]);
        }
    }

    private function makeShelter(string $name, string $email, string $city, string $license): Shelter
    {
        $admin = User::create([
            'name' => $name.' Yöneticisi',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => Role::Admin->value,
            'email_verified_at' => now(),
        ]);

        return Shelter::create([
            'admin_user_id' => $admin->id,
            'name' => $name,
            'license_no' => $license,
            'city' => $city,
            'phone' => '0'.random_int(500, 555).' '.random_int(100, 999).' '.random_int(1000, 9999),
            'address' => $city.' merkez, Barınak Sokak No: '.random_int(1, 80),
            'status' => ShelterStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    private function makePendingShelter(string $name, string $email, string $city, string $license): void
    {
        $admin = User::create([
            'name' => $name.' Yöneticisi',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => Role::Admin->value,
            'email_verified_at' => now(),
        ]);

        Shelter::create([
            'admin_user_id' => $admin->id,
            'name' => $name,
            'license_no' => $license,
            'city' => $city,
            'phone' => '0224 222 3344',
            'address' => $city.' merkez, Yeni Mahalle No: 12',
            'status' => ShelterStatus::Pending->value,
        ]);
    }

    /**
     * @param  array<int, Shelter>  $shelters
     */
    private function seedAnimals(array $shelters): void
    {
        $data = [
            ['Poyraz', 'dog', '3 yaş', 'male', 'Rüzgar gibi koşar, hafifçe durur. Sokakta bulundu, şimdi yuva arıyor.', 'Genel sağlığı iyi, kısırlaştırma bekliyor.'],
            ['Leyla', 'cat', '5 yaş', 'female', 'Her sese miyavlar, her şeye gülüyor gibi. Üç ay sonra güvenmeyi yeniden öğrendi.', 'İç parazit tedavisi tamamlandı.'],
            ['Badem', 'puppy', '9 ay', 'male', 'Her şeyi koklar, her şeye sarılır. Meraklı bir sosyal tomurcuk.', 'Aşı takvimi devam ediyor.'],
            ['Saffron', 'dog', '6 yaş', 'female', 'Küçük bir aile arıyor, sessizce. Misafirleri kapıda karşılamayı sever.', 'Yaşına göre sağlıklı, düzenli kontrol gerekiyor.'],
            ['Toto', 'dog', '2 yaş', 'male', 'Kalbi büyük, dünyaya alışıyor henüz. Sabırlı bir ev arıyor.', 'Beslenme desteğine ihtiyacı var.'],
            ['Luna', 'cat', '4 yaş', 'female', 'Pencereyi sever, kucağı daha çok. Her sabah kuşları izler.', 'Genel sağlığı çok iyi.'],
            ['Pamuk', 'kitten', '4 aylık', 'female', 'Minik bir tüy yumağı. İlk kez insan eli görüyor.', 'Karma aşı bekliyor.'],
            ['Çakıl', 'dog', '7 yaş', 'male', 'Yaşlı ve bilge bir dost. Sakin bir kucak istiyor.', 'Eklem desteği tedavisi sürüyor.'],
            ['Mırnav', 'cat', '2 yaş', 'unknown', 'Utangaç ama meraklı. Yavaşça açılıyor.', 'Göz enfeksiyonu tedavi ediliyor.'],
            ['Karamel', 'puppy', '6 aylık', 'female', 'Enerji dolu, herkesle arkadaş. Oyunbaz bir yavru.', 'Aşıları yapıldı, kısırlaştırma bekliyor.'],
            ['Zeytin', 'cat', '3 yaş', 'male', 'Sessiz bir gözlemci. Yumuşak bir köşe yeter ona.', 'Diş tedavisi gerekiyor.'],
            ['Fındık', 'dog', '1 yaş', 'female', 'Hayatına yeni başlıyor. Her güne koşarak uyanıyor.', 'Sağlığı iyi, mama desteği gerekiyor.'],
        ];

        $needTemplates = [
            ['food', 'Aylık mama desteği', 'Düzenli ve dengeli beslenme için aylık mama ihtiyacı.', 1200],
            ['vaccine', 'Karma aşı', 'Koruyucu karma aşı uygulaması için destek.', 450],
            ['illness', 'Tedavi masrafı', 'Devam eden tedavi süreci için veteriner masrafları.', 3200],
        ];

        $i = 0;
        foreach ($data as [$name, $species, $age, $gender, $story, $health]) {
            $shelter = $shelters[$i % count($shelters)];

            $animal = Animal::create([
                'shelter_id' => $shelter->id,
                'name' => $name,
                'species' => $species,
                'age_estimate' => $age,
                'gender' => $gender,
                'story' => $story,
                'health_status' => $health,
                'is_active' => true,
            ]);

            // Her hayvana 1-2 ihtiyaç
            $needCount = ($i % 2 === 0) ? 2 : 1;
            for ($n = 0; $n < $needCount; $n++) {
                [$type, $title, $desc, $target] = $needTemplates[($i + $n) % count($needTemplates)];
                Need::create([
                    'animal_id' => $animal->id,
                    'shelter_id' => $shelter->id,
                    'type' => $type,
                    'title' => $name.' için '.$title,
                    'description' => $desc,
                    'target_amount' => $target,
                    'status' => 'active',
                ]);
            }

            $i++;
        }
    }

    /**
     * @param  array<int, User>  $donors
     */
    private function seedDonations(array $donors): void
    {
        $service = app(DonationService::class);
        $needs = Need::withoutGlobalScopes()->get();

        $plan = [
            // [donorIndex, needIndex, amount, anonim]
            [0, 0, 800, false],
            [0, 1, 450, false],
            [0, 3, 1500, false],
            [1, 0, 400, false],
            [1, 2, 2000, true],
            [2, 4, 250, false],
            [2, 5, 600, false],
            [2, 6, 3200, false],
            [3, 1, 120, true],
            [0, 7, 5200, false],
            [1, 8, 900, false],
            [2, 2, 1200, false],
        ];

        foreach ($plan as [$d, $nIndex, $amount, $anon]) {
            $need = $needs[$nIndex % $needs->count()] ?? null;
            if ($need === null || ! $need->fresh()->isActive()) {
                continue;
            }

            $service->create($donors[$d], [
                'need_id' => $need->id,
                'amount' => $amount,
                'is_anonymous' => $anon,
                'card_number' => '4242 4242 4242 '.random_int(1000, 9999),
                'card_holder' => $donors[$d]->name,
            ]);
        }
    }

    /**
     * Bağış almış hayvanlara iyileşme günlüğü kayıtları ekler ve
     * o hayvanın bağışçılarına bildirim gönderir.
     */
    private function seedRecoveryUpdates(): void
    {
        // Hayvan başına iyileşme güncellemesi şablonları: [başlık, not, fotoğraf URL'leri].
        $templates = [
            [
                'Tedavisine başlandı 🩺',
                'Veterinerimiz ilk muayeneyi tamamladı. Tahliller iyi, tedavi süreci planlandı. Yakında daha güzel haberler paylaşacağız.',
                [
                    'https://images.unsplash.com/photo-1559190394-df5a28aab5c5?q=80&w=700&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?q=80&w=700&auto=format&fit=crop',
                ],
            ],
            [
                'İlk adımlarını attı 🐾',
                'Bugün bahçede ilk kez koşturdu! İştahı yerinde, gözlerindeki ışık geri geldi. Desteğiniz olmasaydı bu mümkün olmazdı.',
                [
                    'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=700&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=700&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?q=80&w=700&auto=format&fit=crop',
                ],
            ],
            [
                'Artık çok daha mutlu 💛',
                'Sosyalleşme süreci harika gidiyor. Diğer dostlarıyla oyun oynuyor, insanlarla arası çok iyi. Yuvaya hazırlanıyor.',
                [
                    'https://images.unsplash.com/photo-1450778869180-41d0601e046e?q=80&w=700&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1574144611937-0df059b5ef3e?q=80&w=700&auto=format&fit=crop',
                ],
            ],
        ];

        // Bağış almış (ihtiyacı üzerinden) hayvanları bul.
        $needIds = Donation::whereNotNull('need_id')->distinct()->pluck('need_id');
        $animalIds = Need::withoutGlobalScopes()->whereIn('id', $needIds)->pluck('animal_id')->unique();
        $animals = Animal::withoutGlobalScopes()->whereIn('id', $animalIds)->get();

        $t = 0;
        foreach ($animals as $animal) {
            // Her hayvana 1-2 güncelleme — eskiden yeniye doğru.
            $updateCount = ($t % 2 === 0) ? 2 : 1;

            for ($u = 0; $u < $updateCount; $u++) {
                [$title, $note, $photos] = $templates[($t + $u) % count($templates)];

                $update = RecoveryUpdate::create([
                    'animal_id' => $animal->id,
                    'shelter_id' => $animal->shelter_id,
                    'title' => $title,
                    'note' => $note,
                ]);

                foreach ($photos as $index => $photoUrl) {
                    $update->images()->create([
                        'image_path' => $photoUrl,
                        'sort_order' => $index,
                    ]);
                }

                // Bu hayvana doğrudan ya da ihtiyaçları üzerinden bağış yapanları bildir.
                $animalNeedIds = $animal->needs()->withoutGlobalScopes()->pluck('id');
                $donorIds = Donation::where(function ($q) use ($animal, $animalNeedIds) {
                    $q->where('animal_id', $animal->id)
                        ->orWhereIn('need_id', $animalNeedIds);
                })->whereNotNull('user_id')->distinct()->pluck('user_id');

                $recipients = User::whereIn('id', $donorIds)->get();
                Notification::send($recipients, new RecoveryUpdatePublishedNotification($update));
            }

            $t++;
        }
    }

    /**
     * Sertifikası olmayan tüm bağışlar için teşekkür belgesi üretir.
     * (Bağışlar DonationCreated event'i ile zaten belge üretir; bu güvenlik ağıdır.)
     */
    private function seedCertificates(): void
    {
        $action = app(CreateCertificateAction::class);

        $donations = Donation::whereNotNull('user_id')
            ->whereDoesntHave('certificate')
            ->with(['user', 'animal'])
            ->get();

        foreach ($donations as $donation) {
            $action->execute($donation);
        }
    }
}
