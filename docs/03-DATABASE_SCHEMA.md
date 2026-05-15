# 03 — Database Schema

Tüm tablolar (aksi belirtilmedikçe) `created_at` / `updated_at` taşır. Tutar alanları
`decimal(12,2)`. Geliştirme veritabanı: **SQLite**.

---

## shelters — Barınaklar (Tenant)

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| admin_user_id | FK users | Barınağı yöneten admin |
| name | string | Barınak adı |
| license_no | string unique | Ruhsat numarası |
| city | string (index) | Şehir — filtre |
| phone | string | İletişim telefonu |
| address | text | Açık adres |
| status | string/enum | `pending`, `approved`, `rejected`, `suspended` |
| approved_at | timestamp nullable | Superadmin onay tarihi |

## users

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| name | string | Ad Soyad |
| email | string unique | |
| email_verified_at | timestamp nullable | Breeze |
| password | string | Hashed |
| role | string/enum | `superadmin`, `admin`, `user`, `veterinarian` |
| total_donated | decimal(12,2) default 0 | **Denormalize:** global toplam bağış |
| badge_level | tinyint default 0 | 0–5 (0 = rozet yok) |
| is_banned | boolean default false | Superadmin ban |
| remember_token | string nullable | |

## animals

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| shelter_id | FK shelters (index) | Tenant kolonu |
| name | string | |
| species | string/enum | `cat`, `dog`, `kitten`, `puppy` |
| age_estimate | string | Örn. "2 yaş" |
| gender | string/enum | `male`, `female`, `unknown` |
| story | text | Hikaye |
| health_status | text | Sağlık durumu özeti |
| photo_path | string nullable | Tek fotoğraf (Faz 2: galeri) |
| is_active | boolean default true | Yayın durumu |

## needs

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| animal_id | FK animals (index) | |
| shelter_id | FK shelters (index) | Tenant kolonu (animal'dan denormalize) |
| type | string/enum | `food`, `vaccine`, `illness` |
| title | string | |
| description | text nullable | |
| target_amount | decimal(12,2) | Hedef tutar |
| collected_amount | decimal(12,2) default 0 | **Denormalize:** toplanan |
| status | string/enum | `active`, `completed`, `cancelled` |
| completed_at | timestamp nullable | Hedef dolunca otomatik |

## donations

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| user_id | FK users (index) | |
| shelter_id | FK shelters (index) | Denormalize, sorgu için |
| animal_id | FK animals nullable | Barınak genel desteğinde NULL |
| need_id | FK needs nullable | Barınak genel desteğinde NULL |
| amount | decimal(12,2) | Bağış tutarı |
| currency | char(3) default 'TRY' | Faz 1 sabit |
| is_anonymous | boolean default false | Leaderboard'da "Anonim Bağışçı" |
| payment_meta | json | Yalnızca kart son 4 hane — CVV/tam numara ASLA |
| created_at | timestamp (index) | Bu ay/yıl sıralaması için |

> `donations` tablosu `updated_at` taşımaz (kayıt değişmez); yalnızca `created_at`.

## badges — Rozet Tanımları (seed)

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| level | tinyint unique | 1–5 |
| name | string | Rozet adı |
| min_amount | decimal(12,2) | Minimum toplam bağış eşiği |

Seed (`BadgeSeeder`):

| level | min_amount | name |
|---|---|---|
| 1 | 50 | Bronz Patiseven |
| 2 | 500 | Gümüş Koruyucu |
| 3 | 5.000 | Altın Hami |
| 4 | 25.000 | Platin Yardımcı |
| 5 | 200.000 | Elmas Şampiyon |

## announcements

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| shelter_id | FK shelters (index) | Yayınlayan barınak |
| title | string | |
| body | text | |

## notifications — Laravel native

Laravel'in standart `notifications` tablosu (`DatabaseChannel`). Migrasyonu
`php artisan notifications:table` ile gelir. Bildirim tipleri:
[10-NOTIFICATIONS.md](./10-NOTIFICATIONS.md).

## certificates — Faz 2

| Alan | Tip | Açıklama |
|---|---|---|
| id | bigint PK | |
| user_id | FK users | |
| donation_id | FK donations | |
| pdf_path | string | Üretilen PDF yolu |
| veterinarian_id | FK users nullable | Onaylayan veteriner |

---

## Migration Sırası

```
users → shelters → animals → needs → donations → badges
      → announcements → notifications → (Faz 2) certificates
```

> `users` ve `shelters` arasında çapraz FK (`shelters.admin_user_id`,
> `users` admin'in barınağı) olduğundan: önce `users` ve `shelters` tablosu
> FK olmadan oluşturulur, `shelters.admin_user_id` FK'si ayrı bir migration ile
> ya da `shelters` `users`'tan sonra eklendiğinden doğrudan tanımlanır.

## FK Silme Davranışları

| İlişki | Davranış |
|---|---|
| `animals.shelter_id` | cascade |
| `needs.animal_id`, `needs.shelter_id` | cascade |
| `donations.*` | restrict — bağış geçmişi korunur, asla silinmez |
| `announcements.shelter_id` | cascade |

---

**Sonraki:** [04-BUSINESS_RULES.md](./04-BUSINESS_RULES.md)
