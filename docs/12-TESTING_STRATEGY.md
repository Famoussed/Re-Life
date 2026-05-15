# 12 — Testing Strategy

Test framework: **Pest**. Veritabanı: testlerde in-memory SQLite (`:memory:`).

---

## 1. Test Türleri

| Tür | Klasör | Kapsam |
|---|---|---|
| **Unit** | `tests/Unit/` | Action, Enum `label()`, izole hesaplama (rozet seviyesi) |
| **Feature** | `tests/Feature/` | Livewire bileşeni, Service akışı, route koruması, event chain |

> Service ve Action katmanları HTTP'den bağımsız olduğundan doğrudan test edilebilir —
> katmanlı mimarinin başlıca getirisi.

---

## 2. Öncelikli Test Senaryoları

### Bağış & Event Chain
- `DonationService::create()` `donations` kaydı oluşturur, `payment_meta` yalnızca son 4 hane.
- `DonationCreated` sonrası `need.collected_amount` artar.
- Hedef dolunca `need.status = completed` + `NeedCompletedNotification` gönderilir.
- `total_donated` ve `badge_level` doğru hesaplanır; seviye atlamada `BadgeEarnedNotification`.
- `completed`/`cancelled` ihtiyaca bağış reddedilir.

### Multi-Tenancy
- Admin yalnızca kendi barınağının `Animal`/`Need` kayıtlarını sorgular (`ShelterScope`).
- Superadmin tüm veriyi görür.
- Admin başka barınağın kaydını ID ile çağırırsa Policy reddeder.

### Auth & Onay
- Admin kaydı `pending` barınak oluşturur; admin paneline erişemez.
- Superadmin onayı sonrası admin panele girer.
- Banlı kullanıcı giriş yapamaz.

### Leaderboard
- Üç sekme doğru sıralar.
- Tüm bağışları anonim olan kullanıcı "Anonim Bağışçı" görünür; karma durumda gerçek isim.

---

## 3. Kurallar

- Her `Model` için `Factory` bulunur; testler factory ile veri üretir.
- `RefreshDatabase` trait'i Feature testlerinde kullanılır.
- Livewire bileşenleri `Livewire::test()` ile test edilir.
- Bildirim testlerinde `Notification::fake()`, event testlerinde `Event::fake()` kullanılır.
- Hedef: kritik iş kuralları (event chain, tenancy, onay) %100 kapsanır.

---

## 4. Çalıştırma

```bash
php artisan test            # tüm testler
php artisan test --filter=Donation
./vendor/bin/pest --coverage
```

---

**Sonraki:** [13-ROADMAP.md](./13-ROADMAP.md)
