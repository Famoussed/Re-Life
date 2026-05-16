# 11 — Implementation Plan

Sprint bazlı görev kırılımı. Her iş kalemi şu sırayla ilerler:

```
migration → model → enum → service/action → event/listener → livewire → blade → test
```

Bir sprintin **kabul kriterleri** karşılanmadan sonrakine geçilmez.

---

## Güncel Durum

> **MVP iskeleti çalışır durumda.** Sprint 0–7 büyük ölçüde tamamlandı; Sprint 8 (test +
> polish) ve aşağıdaki "Eksikler" bölümü açık. Proje modüler yapıya (5 modül) geçirildi.

| Sprint | Durum |
|---|---|
| 0 — Kurulum (Laravel 11 + Livewire 3 + Breeze + SQLite) | ✅ |
| 1 — Auth + roller + admin onayı | ✅ |
| 2 — Shelter/Animal/Need + admin yönetimi | ✅ (foto upload hariç) |
| 3 — Public anasayfa + detay + filtreler | ✅ |
| 4 — Bağış akışı + göstermelik ödeme | ✅ |
| 5 — Event chain (ihtiyaç + rozet + bildirim) | ✅ |
| 6 — Kullanıcı profili + leaderboard | ✅ |
| 7 — Duyurular + bildirim merkezi | ✅ |
| 8 — Test + polish | ⏳ Kısmi — bkz. Eksikler |

## Modül Sahipliği (3 kişilik ekip)

Paralel geliştirme için önerilen dağılım. Her geliştirici kendi modül(ler)inin
`app/*/​<Modül>/` klasörlerini ve `resources/views/livewire/<modül>/` view'larını sahiplenir.

| Geliştirici | Modüller | Ana sorumluluk |
|---|---|---|
| 1 | **Shelter** + **Notification** | Barınak, duyuru, onay akışı, paneller, bildirimler |
| 2 | **Animal** | Hayvan + ihtiyaç kataloğu, public liste/detay, admin yönetimi |
| 3 | **Donation** + **Account** | Bağış akışı, event chain, rozet, leaderboard, kullanıcı/auth |

Ortak dosyalarda (örn. `routes/web.php`, `database/migrations/`, layout'lar) değişiklik
yapılırken kısa senkronizasyon önerilir.

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

## Eksikler ve Tamamlanması Gereken İş Kalemleri

MVP iskeleti çalışıyor; aşağıdaki kalemler Faz 1'i "tamamlanmış" saymak için kapatılmalı.
Modül etiketleri sahiplik içindir.

### Sprint 8 — Test & Sağlamlaştırma (açık)

| # | İş kalemi | Modül | Öncelik |
|---|---|---|---|
| 8.1 | Pest test paketi: bağış event chain, multi-tenancy (ShelterScope) izolasyonu, admin onay akışı, leaderboard anonimlik kuralı | tümü | Yüksek |
| 8.2 | Her model için `Factory` (testlerin temeli) | tümü | Yüksek |
| 8.3 | Hayvan **fotoğraf yükleme** (`AnimalManager` — Livewire `WithFileUploads`, `storage/app/public`) | Animal | Yüksek |
| 8.4 | Liste sayfalarına **pagination** (anasayfa, leaderboard, panel listeleri) | tümü | Orta |
| 8.5 | `DonationList` CSV export'unun doğrulanması/tamamlanması | Donation | Orta |
| 8.6 | Yetki **Policy sınıfları** (`AnimalPolicy`, `NeedPolicy` vb.) — şu an yalnızca middleware + `ShelterScope` var; [05-RBAC](./05-RBAC_PERMISSIONS.md)'deki iki-kat savunma için Policy eklenmeli | tümü | Orta |
| 8.7 | E-posta doğrulama akışının uçtan uca testi (Breeze; seed kullanıcılar ön-doğrulanmış) | Account | Düşük |
| 8.8 | Boş/hata durumları, mobil responsive ve erişilebilirlik geçişi | tümü | Orta |
| 8.9 | `decimal` cast'inde PHP 8.4 float deprecation'ı — tutar hesaplarında string cast | Donation | Düşük |

### Ekip Süreci

- Modül sahipliği: yukarıdaki **Modül Sahipliği** tablosu.
- Branch başına bir iş kalemi; ortak dosya (route, migration, layout) değişiminde senkron.
- Her iş kalemi kabul kriteri + ilgili Pest testiyle birlikte kapanır.

### Kapsam Dışı (Faz 2)

Veteriner rolü, otomatik PDF sertifika, foto galerisi, e-posta/push bildirim, prod
altyapısı — [13-ROADMAP.md](./13-ROADMAP.md).

---

> Test stratejisi: [12-TESTING_STRATEGY.md](./12-TESTING_STRATEGY.md).

**Sonraki:** [12-TESTING_STRATEGY.md](./12-TESTING_STRATEGY.md)
