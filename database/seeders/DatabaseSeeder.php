<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\ShelterStatus;
use App\Models\Animal;
use App\Models\Badge;
use App\Models\Need;
use App\Models\Shelter;
use App\Models\User;
use App\Services\DonationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
}
