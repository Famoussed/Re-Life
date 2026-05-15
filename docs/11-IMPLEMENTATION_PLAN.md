# 11 — Implementation Plan

Sprint bazlı görev kırılımı. Her iş kalemi şu sırayla ilerler:

```
migration → model → enum → policy → service/action → event/listener → livewire → blade → test
```

Bir sprintin **kabul kriterleri** karşılanmadan sonrakine geçilmez.

---

## Sprint 0 — Proje Kurulumu

İskelet dizinleri üzerine gerçek Laravel projesi kurulur.

- `composer create-project laravel/laravel` (Laravel 11)
- Livewire 3, Laravel Breeze (Livewire stack) kurulumu
- Tailwind + Alpine yapılandırması
- `.env`: `DB_CONNECTION=sqlite`, `QUEUE_CONNECTION=sync`, `CACHE_STORE=database`
- `App\Enums` sınıfları + `App\Scopes\ShelterScope`
- `.claude/settings.json` Pint hook'u aktive edilir

**Kabul:** `php artisan serve` çalışır, boş anasayfa açılır.

## Sprint 1 — Auth + Roller + Admin Onayı

- Migration: `users`, `shelters`
- Model: `User` (Notifiable), `Shelter` + ilişkiler
- Middleware: `EnsureUserRole`, `EnsureShelterApproved`
- Breeze user kayıt/giriş; `Auth\RegisterShelterAdmin` Livewire bileşeni
- `RegisterShelterAdminService`, `ShelterService` (approve/reject), `ApproveShelterAction`
- `Superadmin\ShelterApprovals` Livewire bileşeni
- `AdminRegistrationStatusNotification`
- Policy: `ShelterPolicy`, `UserPolicy`

**Kabul:** User kayıt+giriş yapar; admin kayıt sonrası panele giremez; superadmin onaylar;
onaylı admin admin paneline girer.

## Sprint 2 — Shelter & Animal & Need + Admin Yönetim

- Migration: `animals`, `needs`
- Model: `Animal`, `Need` + `ShelterScope`
- Policy: `AnimalPolicy`, `NeedPolicy`
- `AnimalService`, `NeedService`
- Livewire: `Admin\AnimalManager`, `Admin\NeedManager`, `Admin\ShelterProfileEdit`
- Factory + `DemoDataSeeder`

**Kabul:** Admin kendi barınağına hayvan/ihtiyaç ekler; başka barınağın verisini görmez;
superadmin tümünü görür; `completed` ihtiyaç düzenlenemez.

## Sprint 3 — Public Anasayfa + Detay + Filtreler

- Livewire: `Public\AnimalList` (filtreler), `Public\AnimalDetail`, `Public\ShelterProfile`
- Blade: kart grid, progress bar, animal-card paylaşılan bileşenleri
- Filtrelerin query string'e yansıması

**Kabul:** Misafir hayvanları görür ve filtreler; hayvan detayında aktif ihtiyaçlar ve
ilerleme çubukları görünür.

## Sprint 4 — Bağış Akışı + Göstermelik Ödeme

- Migration: `donations`
- Model: `Donation`
- `DonationService::create()` + `CreateDonationAction`
- Livewire: `Donation\DonationFlow` (scope → miktar → sahte ödeme → başarı)
- Policy: `DonationPolicy`

**Kabul:** Donor bağış yapar; `donations` kaydı oluşur; `payment_meta` yalnızca son 4 hane;
tamamlanmış ihtiyaca bağış engellenir.

## Sprint 5 — Event Chain

- `DonationCreated` event
- Listener: `UpdateNeedProgressListener`, `UpdateUserBadgeListener`, `NotifyShelterMetricsListener`
- Action: `ApplyNeedProgressAction`, `RecalculateUserBadgeAction`
- Migration + seeder: `badges` (`BadgeSeeder`), `notifications`
- Notification: `NeedCompletedNotification`, `BadgeEarnedNotification`

**Kabul:** Bağış sonrası `collected_amount` artar, hedef dolunca ihtiyaç kapanır ve
destekçiler bilgilendirilir; `total_donated`/`badge_level` güncellenir, seviye atlamada bildirim.

## Sprint 6 — Kullanıcı Profili + Leaderboard

- Livewire: `Public\UserProfile` (public + sahip görünümü), `Public\Leaderboard` (3 sekme)
- Bu ay/bu yıl `SUM` sorguları + anonimlik kuralı
- Profil düzenleme

**Kabul:** Profilde rozet, toplam bağış, geçmiş, desteklenen hayvanlar; leaderboard 3
sekmede doğru sıralar; anonimlik kuralı uygulanır.

## Sprint 7 — Duyurular + Bildirim Merkezi

- Migration: `announcements`
- Model: `Announcement` + `AnnouncementService::publish()`
- Livewire: `Admin\AnnouncementManager`, `Notification\NotificationCenter`
- `ShelterAnnouncementNotification`

**Kabul:** Admin duyuru yayınlar; yalnızca o barınağın bağışçıları bildirim alır; kullanıcı
bildirim merkezinde görüp okundu işaretler.

## Sprint 8 — Polish + Test

- `Admin\Dashboard`, `Admin\DonationList` (CSV export), `Admin\DonorList`
- `Superadmin\Dashboard`, `Superadmin\ShelterList`, `Superadmin\UserList`, `Superadmin\BadgeManager`
- Pest testleri: bağış event chain, multi-tenancy izolasyonu, onay akışı, leaderboard
- Responsive/erişilebilirlik kontrolü

**Kabul:** Tüm Faz 1 fonksiyonları çalışır; testler yeşil; multi-tenancy izolasyonu testle
doğrulanmış.

---

> Faz 2 (veteriner, sertifika): [13-ROADMAP.md](./13-ROADMAP.md).
> Test stratejisi: [12-TESTING_STRATEGY.md](./12-TESTING_STRATEGY.md).

**Sonraki:** [12-TESTING_STRATEGY.md](./12-TESTING_STRATEGY.md)
