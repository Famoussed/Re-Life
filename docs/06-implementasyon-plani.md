# 06 — İmplementasyon Planı

## Genel İlke

Her sprintte iş kalemleri şu sırayla ilerler:

```
migration → model → enum → policy → action/event/listener → livewire/filament → blade view → test
```

Her sprintin sonunda **kabul kriterleri** karşılanmadan sonraki sprinte geçilmez.

## Sprint 0 — Proje Kurulumu (scaffold)

İskelet dizinleri üzerine gerçek Laravel projesi kurulur.

- `composer create-project laravel/laravel` (Laravel 11)
- Livewire 3, Filament 3, Laravel Breeze (Livewire stack) kurulumu
- Tailwind yapılandırması, `.env` + veritabanı bağlantısı
- `App\Enums` sınıfları: `Role`, `ShelterStatus`, `AnimalSpecies`, `Gender`, `NeedType`, `NeedStatus`
- `.claude/settings.json` Pint hook'u aktive edilir

**Kabul:** `php artisan serve` çalışır, boş anasayfa açılır, Filament panel route'ları yanıt verir.

## Sprint 1 — Auth + Roller + Admin Onayı

- Migration: `users` (role, total_donated, badge_level, is_banned), `shelters`
- Model: `User`, `Shelter` + ilişkiler
- `User::canAccessPanel()` rol/onay mantığı
- Breeze: user kayıt/giriş; `/admin/register` özel formu (`pending` barınak)
- Filament `superadmin` paneli: **Admin Onayları** kaynağı (onayla/reddet)
- `AdminRegistrationStatusNotification`
- Policy: `ShelterPolicy`, `UserPolicy`

**Kabul:** User kayıt+giriş yapar; admin kayıt sonrası panele giremez; superadmin onaylar;
onaylı admin `/admin` paneline girer.

## Sprint 2 — Shelter & Animal & Need + Admin CRUD

- Migration: `animals`, `needs`
- Model: `Animal`, `Need` + `ShelterScope` global scope
- Policy: `AnimalPolicy`, `NeedPolicy`
- Filament `admin` paneli: Hayvanlar CRUD (foto upload), İhtiyaçlar CRUD, Barınak Profili
- Factory + seeder (örnek barınak/hayvan/ihtiyaç)

**Kabul:** Admin kendi barınağına hayvan/ihtiyaç ekler; başka barınağın verisini görmez;
superadmin tüm veriyi görür.

## Sprint 3 — Public Anasayfa + Detay + Filtreler

- Livewire: `AnimalList` (filtreler: tür, şehir, ihtiyaç tipi), `AnimalDetail`, `ShelterProfile`
- Blade view + Tailwind kart grid + progress bar bileşeni
- Filtrelerin query string'e yansıması

**Kabul:** Misafir anasayfada hayvanları görür, filtreler; hayvan detayında aktif ihtiyaçlar
ve ilerleme çubukları görünür.

## Sprint 4 — Bağış Akışı + Göstermelik Ödeme

- Migration: `donations`
- Model: `Donation`
- `App\Actions\CreateDonationAction` (transaction + doğrulama)
- Livewire: `DonationFlow` (scope seçimi → miktar → sahte ödeme → başarı)
- Policy: `DonationPolicy`

**Kabul:** Donor giriş yapıp bağış yapar; `donations` kaydı oluşur; `payment_meta` yalnızca
son 4 hane içerir; tamamlanmış ihtiyaca bağış engellenir.

## Sprint 5 — Event Chain

- `DonationCreated` event
- Listener: `UpdateNeedProgress`, `UpdateUserBadge`, `UpdateShelterMetrics`
- Migration + seeder: `badges` (`BadgeSeeder`)
- Notification: `NeedCompletedNotification`, `BadgeEarnedNotification`

**Kabul:** Bağış sonrası `collected_amount` artar, hedef dolunca ihtiyaç kapanır ve destekçiler
bilgilendirilir; `total_donated` ve `badge_level` güncellenir, seviye atlamada bildirim gider.

## Sprint 6 — Kullanıcı Profili + Leaderboard

- Livewire: `UserProfile` (public + sahip görünümü), `Leaderboard` (3 sekme)
- Bu ay / bu yıl `SUM` sorguları + anonimlik kuralı
- Profil düzenleme

**Kabul:** Profilde rozet, toplam bağış, geçmiş, desteklenen hayvanlar görünür; leaderboard
3 sekmede doğru sıralar; anonim bağışçı kuralı uygulanır.

## Sprint 7 — Duyurular + Bildirim Merkezi

- Migration: `announcements`
- Model: `Announcement` + Filament admin CRUD
- `ShelterAnnouncementNotification` (hedef kitle: barınağın bağışçıları)
- Livewire: `NotificationCenter` (`/me/notifications`)

**Kabul:** Admin duyuru yayınlar; yalnızca o barınağın bağışçıları bildirim alır; kullanıcı
bildirim merkezinde görüp okundu işaretler.

## Sprint 8 — Polish, Test, Deployment

- Filament admin: Bağışlar CSV export, Dashboard widget'ları, Bağışçılar listesi
- Superadmin: Tüm Barınaklar (suspend/aktive), Tüm Kullanıcılar (ban/unban), Global İstatistikler
- Pest testleri: kritik akışlar (bağış event chain, multi-tenancy izolasyonu, onay akışı)
- Erişilebilirlik / responsive kontrol, deployment yapılandırması

**Kabul:** Tüm Faz 1 fonksiyonları çalışır; testler yeşil; multi-tenancy izolasyonu testle doğrulanmış.

## Sprint Eşlemesi (spec §9 ile)

| Bu plan | spec §9 |
|---|---|
| Sprint 0 | (scaffold — spec'te ayrı kalem değil) |
| Sprint 1 | Sprint 1 |
| Sprint 2 | Sprint 2 |
| Sprint 3 | Sprint 3 |
| Sprint 4 | Sprint 4 |
| Sprint 5 | Sprint 5 |
| Sprint 6 | Sprint 6 |
| Sprint 7 | Sprint 7 |
| Sprint 8 | Sprint 8 |

> Faz 2 kapsamı bu plana dahil değildir — bkz. [08-faz-2.md](08-faz-2.md).
