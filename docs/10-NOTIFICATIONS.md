# 10 — Notifications

In-app bildirim sistemi. Laravel'in native `notifications` tablosu +
`DatabaseChannel` kullanılır. **WebSocket/push yoktur** — bildirimler sayfada
gösterilir, kullanıcı yenilemede görür.

---

## 1. Kanal

- Tek kanal: `database`. E-posta/push Faz 2 kapsamında ele alınır.
- `User` modeli `Notifiable` trait'ini kullanır.
- Migrasyon: `php artisan notifications:table` (Sprint 0/5).

---

## 2. Bildirim Tipleri

| Notification | Tetikleyici | Alıcı | İçerik |
|---|---|---|---|
| `NeedCompletedNotification` | Desteklenen ihtiyaç hedefe ulaştı | İhtiyaca bağış yapan tüm kullanıcılar | Hayvan adı, ihtiyaç başlığı |
| `BadgeEarnedNotification` | Yeni rozet seviyesi | İlgili kullanıcı | Yeni rozet adı/seviyesi |
| `ShelterAnnouncementNotification` | Barınak duyuru yayınladı | Barınağın bağışçıları | Duyuru başlığı, barınak adı |
| `AdminRegistrationStatusNotification` | Admin başvurusu sonuçlandı | İlgili admin | Onaylandı / reddedildi |

---

## 3. Tetiklenme Noktaları

```
DonationCreated event
  → UpdateNeedProgressListener  → (ihtiyaç tamamlandıysa) NeedCompletedNotification
  → UpdateUserBadgeListener     → (seviye atladıysa) BadgeEarnedNotification

AnnouncementService::publish()  → ShelterAnnouncementNotification
ShelterService::approve()/reject() → AdminRegistrationStatusNotification
```

Tüm gönderimler senkron (`ShouldQueue` kullanılmaz — MVP).

---

## 4. Bildirim Merkezi

`Notification\NotificationCenter` Livewire bileşeni (`/me/notifications`):

- Kullanıcının bildirimlerini tarihe göre listeler.
- Okundu/okunmadı ayrımı; "tümünü okundu işaretle".
- Uygulama layout'unda okunmamış sayısını gösteren bir badge (basit sorgu, polling yok).

---

## 5. Hedef Kitle Hesabı

`ShelterAnnouncementNotification` ve `NeedCompletedNotification` için alıcı listesi
`donations` tablosundan `distinct user_id` ile hesaplanır:

```php
// Duyuru
$userIds = Donation::where('shelter_id', $shelterId)->distinct()->pluck('user_id');

// İhtiyaç tamamlandı
$userIds = Donation::where('need_id', $needId)->distinct()->pluck('user_id');
```

---

**Sonraki:** [11-IMPLEMENTATION_PLAN.md](./11-IMPLEMENTATION_PLAN.md)
