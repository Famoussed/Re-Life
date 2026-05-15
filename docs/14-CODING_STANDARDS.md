# 14 — Coding Standards

---

## 1. Genel

- **PSR-12.** Biçimlendirme: `./vendor/bin/pint` — commit öncesi (ve PostToolUse hook'uyla
  otomatik) çalıştırılır.
- **PHP 8.3** — backed enum, readonly property, constructor promotion, typed property kullan.
- Strict types: dosya başında `declare(strict_types=1);`.

## 2. Dil

- **Domain dili Türkçe, kod sembolleri İngilizce.**
  - Sınıf / metod / değişken / kolon / migration adları İngilizce: `Shelter`, `Donation`,
    `collected_amount`, `CreateDonationAction`.
  - Kullanıcıya görünen metinler, validation mesajları, enum `label()` çıktıları, blade
    içeriği, yorumlar ve dokümanlar Türkçe.

## 3. Katman Kuralları (zorunlu)

Bkz. [01-ARCHITECTURE_OVERVIEW.md](./01-ARCHITECTURE_OVERVIEW.md) §3.

- Livewire bileşeni **yalnızca Service** çağırır — Model/Action/`DB` **yasak**.
- İş mantığı Service'te; tek amaçlı operasyon Action'da.
- `DB::transaction` yalnızca Service katmanında açılır.
- Katman atlama yasak: Livewire → Service → Action → Model.

## 4. İsimlendirme

| Tür | Kural | Örnek |
|---|---|---|
| Service | `<Domain>Service` | `DonationService` |
| Action | `<Fiil><Nesne>Action`, tek `execute()` | `CreateDonationAction` |
| Event | Geçmiş zaman | `DonationCreated` |
| Listener | `<Fiil>...Listener` | `UpdateNeedProgressListener` |
| Notification | `<Olay>Notification` | `BadgeEarnedNotification` |
| Policy | `<Model>Policy` | `AnimalPolicy` |
| Livewire | Namespace altında PascalCase | `Admin\AnimalManager` |
| Migration | Laravel standardı, snake_case tablo | `create_donations_table` |
| Enum | PascalCase tip, PascalCase case | `NeedType::Vaccine` |

## 5. Eloquent / Model

- `$fillable` her zaman tanımlı (mass-assignment koruması).
- Enum kolonları `$casts` ile backed enum'a cast edilir.
- Tutar alanları `decimal:2` cast'i.
- Tenant modelleri `ShelterScope`'u `booted()` içinde bağlar.
- İlişki metodları açık dönüş tipi taşır (`: BelongsTo`, `: HasMany`).

## 6. Para & Güvenlik

- Tüm tutarlar `decimal(12,2)`; hesaplamalarda float biriktirme yapılmaz.
- `payment_meta` yalnızca kart numarasının son 4 hanesini saklar. CVV, tam kart numarası,
  son kullanma tarihi **asla** saklanmaz/loglanmaz.
- Denormalize alanlar (`collected_amount`, `total_donated`, `badge_level`) yalnızca ilgili
  listener/action içinde güncellenir.

## 7. Validation

- Livewire bileşenlerinde `rules()` metodu; Controller varsa `FormRequest`.
- Validation mesajları Türkçe.

## 8. Test

- Pest. Her `Model` için `Factory`. Detay: [12-TESTING_STRATEGY.md](./12-TESTING_STRATEGY.md).
- Yeni iş kuralı → ilgili Feature testi aynı sprintte yazılır.

## 9. Yasaklar

- Genel `try/catch` ile hata yutma — anlamlı exception veya Laravel handler.
- Livewire/Controller içinde iş mantığı veya doğrudan sorgu.
- `env()` çağrısını config dışında kullanma — `config()` üzerinden eriş.
- Migration'da `down()` boş bırakma.
