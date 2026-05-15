# 04 — İş Kuralları (Business Rules)

## 1. Bağış Oluşturma

`App\Actions\CreateDonationAction` tek bir DB transaction'ında:

1. Girdi doğrulaması:
   - Miktar > 0.
   - Spesifik bağışta `need.status === active` ve `need` ilgili `animal`/`shelter` ile tutarlı.
   - `animal_id`/`need_id` ya birlikte dolu ya birlikte boş (barınak genel destek).
2. `donations` kaydı oluşturulur. `payment_meta` yalnızca kart numarasının **son 4 hanesini**
   içerir; CVV, tam kart numarası, son kullanma tarihi **asla saklanmaz**.
3. `DonationCreated` event'i fırlatılır.

### Event Chain

```
DonationCreated
  ├─→ UpdateNeedProgress
  ├─→ UpdateUserBadge
  └─→ UpdateShelterMetrics
```

Tüm listener'lar aynı transaction kapsamında, senkron çalışır (MVP). Sıralama önemli değildir;
birbirinden bağımsızdırlar.

## 2. İhtiyaç İlerlemesi & Otomatik Kapanma

`UpdateNeedProgress` listener (yalnızca `need_id` doluysa):

- `need.collected_amount += donation.amount`
- `collected_amount >= target_amount` ise:
  - `status = completed`
  - `completed_at = now()`
  - O ihtiyaca daha önce bağış yapmış tüm kullanıcılara `NeedCompletedNotification`.
- Tamamlanmış (`completed`) veya iptal (`cancelled`) ihtiyaca **yeni bağış kabul edilmez** —
  `DonationFlow` bileşeni ve `CreateDonationAction` doğrulaması engeller.
- Hayvan detayında tamamlanan ihtiyaç "Tamamlandı ✓" rozetiyle gösterilir.

> Hedefin üzerine taşma (`collected > target`) kabul edilir; son bağış kısmen "fazla"
> olabilir. Faz 1'de bölme/iade yapılmaz.

## 3. Rozet Hesabı

`UpdateUserBadge` listener:

- **Baz alınan değer:** Kullanıcının **global toplam** bağışı — tüm barınaklara yapılan tüm
  bağışların toplamı.
- Anonim bağışlar da bu toplama dahildir (rozet kazandırır); anonimlik yalnızca leaderboard
  görünürlüğünü etkiler.
- `user.total_donated` yeniden hesaplanır (`donations` üzerinden `SUM(amount)` veya mevcut
  değere ekleme — denormalize alan tek kaynaktan güncellenir).
- `badge_level` = `min_amount <= total_donated` olan en yüksek `badges.level` (yoksa 0).
- Seviye bir öncekinden yüksekse `BadgeEarnedNotification` gönderilir.

Rozet eşikleri: bkz. [02-veri-modeli.md](02-veri-modeli.md) §3.6.

## 4. Leaderboard (Top 100)

Üç sekme, hepsi en fazla 100 kullanıcı:

| Sekme | Hesap |
|---|---|
| **Tüm Zamanlar** | `users.total_donated DESC LIMIT 100` (banlı kullanıcılar hariç) |
| **Bu Yıl** | `donations` → `SUM(amount) GROUP BY user_id WHERE YEAR(created_at)=...` `DESC LIMIT 100` |
| **Bu Ay** | Aynı, `YEAR` + `MONTH` filtresiyle |

Anonimlik kuralı (karma durum dahil — bkz. [07-acik-konular.md](07-acik-konular.md)):

- Her kullanıcı, sıralama hesabında kendi `user_id`'siyle yer alır.
- **Bir kullanıcının ilgili dönemdeki tüm bağışları anonimse** → satırda isim "Anonim Bağışçı",
  profil linki yok.
- **En az bir isimli bağışı varsa** → gerçek isim ve profil linki gösterilir.

## 5. Duyuru Hedef Kitlesi

`Announcement` oluşturulduğunda:

```
hedef = donations.where('shelter_id', $announcement->shelter_id)
                 ->distinct()->pluck('user_id')
```

Bu kullanıcılara `ShelterAnnouncementNotification` (in-app) gönderilir. Bağış yapmamış
kullanıcılar duyuru almaz.

## 6. Bildirimler

Tümü Laravel `DatabaseChannel` ile `notifications` tablosuna yazılır; kullanıcı bildirim
merkezinde (`/me/notifications`) görür.

| Bildirim | Tetikleyici |
|---|---|
| `NeedCompletedNotification` | Desteklenen ihtiyaç hedefe ulaştı |
| `BadgeEarnedNotification` | Yeni rozet seviyesine geçildi |
| `ShelterAnnouncementNotification` | Destekçisi olunan barınak duyuru yayınladı |
| `AdminRegistrationStatusNotification` | Admin başvurusu onaylandı/reddedildi |

## 7. Admin Onay Kuralı

- Yeni admin kaydı `shelter.status = pending` ile başlar; panele erişemez.
- Yalnızca superadmin `approved` / `rejected` yapabilir.
- `approved` → `approved_at` set edilir.
- `suspended` → onaylı barınak sonradan askıya alınabilir; admin paneli erişimi kapanır,
  barınağın hayvanları/ihtiyaçları public listede gizlenir.

## 8. Veri Bütünlüğü İlkeleri

- `collected_amount`, `total_donated`, `badge_level` denormalize alanlardır; yalnızca ilgili
  listener içinde, transaction altında güncellenir. Manuel düzenlenmez.
- Bağış kayıtları **silinmez** (mali geçmiş). Kullanıcı/barınak silme yerine ban/suspend kullanılır.
- Tamamlanan ihtiyaç ve onaylanan bağış geri alınamaz (Faz 1).
