# 04 — Business Rules

İş kuralları ve bağış event chain'i. İlgili katman: Service + Action + Event/Listener
(bkz. [01-ARCHITECTURE_OVERVIEW.md](./01-ARCHITECTURE_OVERVIEW.md)).

---

## 1. Bağış Oluşturma

Akış: `DonationFlow` Livewire bileşeni → `DonationService::create()` →
`CreateDonationAction` → `DonationCreated` event.

`DonationService::create()` tek bir `DB::transaction` içinde:

1. **Doğrulama:**
   - Tutar > 0.
   - Spesifik bağışta `need.status === active`; `need`, ilgili `animal`/`shelter` ile tutarlı.
   - `animal_id` ve `need_id` ya **birlikte dolu** ya **birlikte boş** (barınak genel destek).
2. `CreateDonationAction` `donations` kaydını oluşturur. `payment_meta` yalnızca kart
   numarasının **son 4 hanesini** içerir; CVV / tam numara / SKT **asla saklanmaz**.
3. `DonationCreated` event'i dispatch edilir.

---

## 2. Event Chain

```
DonationCreated
  ├─→ UpdateNeedProgressListener
  ├─→ UpdateUserBadgeListener
  └─→ NotifyShelterMetricsListener
```

Tüm listener'lar **senkron** çalışır, aynı transaction kapsamındadır (MVP — kuyruk yok).
Birbirinden bağımsızdır, sıra önemli değildir.

---

## 3. İhtiyaç İlerlemesi & Otomatik Kapanma

`UpdateNeedProgressListener` (yalnızca `need_id` doluysa):

- `need.collected_amount += donation.amount`
- `collected_amount >= target_amount` → `status = completed`, `completed_at = now()`.
- İhtiyaç tamamlanınca o ihtiyaca daha önce bağış yapmış **tüm kullanıcılara**
  `NeedCompletedNotification`.
- `completed` veya `cancelled` ihtiyaca **yeni bağış kabul edilmez** — hem `DonationFlow`
  bileşeni hem `DonationService` doğrulaması engeller.
- Hedef aşımı (`collected > target`) kabul edilir; iade/bölme yapılmaz.

---

## 4. Rozet Hesabı

`UpdateUserBadgeListener`:

- **Baz:** Kullanıcının **global toplam** bağışı (tüm barınaklara yapılan tüm bağışların
  toplamı). Anonim bağışlar da dahildir.
- `user.total_donated` güncellenir.
- `badge_level` = `min_amount <= total_donated` olan en yüksek `badges.level` (yoksa 0).
- Seviye bir öncekinden yüksekse `BadgeEarnedNotification` gönderilir.

Rozet eşikleri: [03-DATABASE_SCHEMA.md](./03-DATABASE_SCHEMA.md) (`badges` seed).

---

## 5. Leaderboard (Top 100)

Üç sekme, her biri en fazla 100 kullanıcı (banlı kullanıcılar hariç):

| Sekme | Hesap |
|---|---|
| Tüm Zamanlar | `users.total_donated DESC LIMIT 100` |
| Bu Yıl | `donations` → `SUM(amount) GROUP BY user_id`, `YEAR(created_at)` filtresi |
| Bu Ay | Aynı, `YEAR` + `MONTH` filtresi |

**Anonimlik kuralı (karma durum):**
- Kullanıcı sıralamada kendi `user_id`'siyle yer alır.
- İlgili dönemdeki **tüm** bağışları anonimse → satırda "Anonim Bağışçı", profil linki yok.
- En az bir isimli bağışı varsa → gerçek isim + profil linki.

---

## 6. Duyuru Hedef Kitlesi

`AnnouncementService::publish()` çağrıldığında:

```
hedef = donations.where('shelter_id', X)->distinct()->pluck('user_id')
```

Bu kullanıcılara `ShelterAnnouncementNotification` gönderilir. Barınağa hiç bağış
yapmamış kullanıcı duyuru almaz.

---

## 7. Admin Onay Kuralı

- Yeni admin kaydı `shelter.status = pending` ile başlar; admin paneline erişemez.
- Yalnızca superadmin `approved` / `rejected` yapabilir.
- `approved` → `approved_at` set edilir; `AdminRegistrationStatusNotification` gönderilir.
- `suspended` → onaylı barınak askıya alınır; panel erişimi kapanır, hayvanlar/ihtiyaçlar
  public listeden gizlenir.

---

## 8. Veri Bütünlüğü İlkeleri

- Denormalize alanlar (`collected_amount`, `total_donated`, `badge_level`) yalnızca ilgili
  listener içinde, transaction altında güncellenir. Manuel/panelden düzenlenmez.
- Bağış kayıtları **silinmez** (mali geçmiş). Kullanıcı/barınak için silme yerine
  ban/suspend kullanılır.
- Tamamlanan ihtiyaç ve oluşturulan bağış geri alınamaz (Faz 1).

---

**Sonraki:** [05-RBAC_PERMISSIONS.md](./05-RBAC_PERMISSIONS.md)
