# 09 — Service & Action Catalog

İş mantığını taşıyan Service ve Action sınıflarının kataloğu. Katman kuralı:
Livewire → **Service** → **Action** → Model ([01](./01-ARCHITECTURE_OVERVIEW.md)).

---

## Service ↔ Action Ayrımı

- **Service** — bir iş akışını orkestrasyon eder: doğrulama, `DB::transaction`, birden çok
  Action'ı sıralama, Event dispatch. Bir domain'e karşılık gelir.
- **Action** — tek bir atomik operasyon. Tek `execute()` metodu. Service'ler arası ve
  testlerden yeniden kullanılabilir. Transaction açmaz (Service'in transaction'ı içinde çalışır).

---

## Services

### DonationService
| Metod | İş |
|---|---|
| `create(User $donor, array $data)` | Bağışı doğrula, `DB::transaction` aç, `CreateDonationAction` çağır, `DonationCreated` dispatch et. |

### RegisterShelterAdminService
| Metod | İş |
|---|---|
| `register(array $data)` | Transaction içinde `users` (role=admin) + `shelters` (status=pending) oluştur. |

### ShelterService
| Metod | İş |
|---|---|
| `approve(Shelter $shelter)` | `ApproveShelterAction` + `AdminRegistrationStatusNotification`. |
| `reject(Shelter $shelter)` | Status `rejected` + bildirim. |
| `suspend(Shelter $shelter)` / `activate(...)` | Status değişimi. |

### AnimalService
| Metod | İş |
|---|---|
| `create/update/delete` | Hayvan CRUD + foto yönetimi. |

### NeedService
| Metod | İş |
|---|---|
| `create/update` | İhtiyaç CRUD; `completed` ihtiyaç düzenlemesini reddet. |
| `cancel(Need $need)` | Status `cancelled`. |

### AnnouncementService
| Metod | İş |
|---|---|
| `publish(Shelter $shelter, array $data)` | Duyuru oluştur + barınağın bağışçılarına `ShelterAnnouncementNotification`. |

---

## Actions

| Action | `execute()` görevi |
|---|---|
| `CreateDonationAction` | `donations` kaydını oluşturur; `payment_meta`'ya yalnızca kart son 4 hane. |
| `ApproveShelterAction` | `shelter.status = approved`, `approved_at = now()`. |
| `RecalculateUserBadgeAction` | `total_donated` + `badge_level` yeniden hesaplar; önceki seviyeyi döner. |
| `ApplyNeedProgressAction` | `need.collected_amount` artırır; hedef dolduysa `completed` yapar. |

> `RecalculateUserBadgeAction` ve `ApplyNeedProgressAction`, event listener'larından çağrılır
> (bkz. [04-BUSINESS_RULES.md](./04-BUSINESS_RULES.md) §2). Böylece aynı mantık hem event
> chain'de hem de gerekirse bir bakım komutunda yeniden kullanılır.

---

## Event / Listener / Notification

| Event | Listener'lar |
|---|---|
| `DonationCreated` | `UpdateNeedProgressListener`, `UpdateUserBadgeListener`, `NotifyShelterMetricsListener` |

| Notification | Tetikleyen |
|---|---|
| `NeedCompletedNotification` | `UpdateNeedProgressListener` |
| `BadgeEarnedNotification` | `UpdateUserBadgeListener` |
| `ShelterAnnouncementNotification` | `AnnouncementService::publish()` |
| `AdminRegistrationStatusNotification` | `ShelterService::approve()/reject()` |

Tüm listener'lar senkron çalışır (MVP — kuyruk yok). Detay:
[10-NOTIFICATIONS.md](./10-NOTIFICATIONS.md).

---

**Sonraki:** [10-NOTIFICATIONS.md](./10-NOTIFICATIONS.md)
