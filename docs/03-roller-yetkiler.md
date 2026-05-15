# 03 — Roller ve Yetkiler

## Roller

| Rol | Açıklama | Erişim |
|---|---|---|
| **superadmin** | Platform sahibi, global yönetici | Tüm veri + admin/barınak onayı |
| **admin** | Barınak yöneticisi (1 admin = 1 barınak) | Yalnızca kendi barınağının verisi |
| **user** | Bağışçı (donor) | Public sayfalar + kendi profili |
| **veterinarian** *(Faz 2)* | Barınağa bağlı veteriner | Hayvan sağlık bilgisi + sertifika onayı |

Roller `users.role` enum kolonunda tutulur ve `App\Enums\Role` enum sınıfıyla temsil edilir.

## Yetki Matrisi

| Yetenek | superadmin | admin | user | misafir |
|---|:--:|:--:|:--:|:--:|
| Public sayfaları görüntüle | ✓ | ✓ | ✓ | ✓ |
| Bağış yap | ✓ | ✓ | ✓ | ✗ |
| Hayvan/ihtiyaç CRUD (kendi barınağı) | — | ✓ | ✗ | ✗ |
| Hayvan/ihtiyaç CRUD (tüm barınaklar) | ✓ | ✗ | ✗ | ✗ |
| Duyuru yayınla (kendi barınağı) | — | ✓ | ✗ | ✗ |
| Barınağın bağışçılarını gör | ✓ | ✓ (kendi) | ✗ | ✗ |
| Admin/barınak onayı | ✓ | ✗ | ✗ | ✗ |
| Barınak askıya al / aktive et | ✓ | ✗ | ✗ | ✗ |
| Kullanıcı ban / unban | ✓ | ✗ | ✗ | ✗ |
| Global istatistikler | ✓ | ✗ | ✗ | ✗ |
| Kendi profilini düzenle | ✓ | ✓ | ✓ | ✗ |

> `misafir` = giriş yapmamış ziyaretçi. Bağış yapmak giriş gerektirir.

## Auth Akışları

### User (donor) kaydı

Standart Breeze akışı: ad-soyad, email, şifre → email doğrulama → giriş.
`role = user` olarak oluşturulur. Onay gerektirmez.

### Admin (barınak) kaydı

`/admin/register` formu — standart user alanları **+** barınak bilgileri:

- Ad Soyad, email, şifre (→ `users`, `role = admin`)
- Barınak adı, **ruhsat no**, şehir, telefon, adres (→ `shelters`, `status = pending`)

Akış:

1. Kayıt → `users` (role=admin) + `shelters` (status=pending) aynı transaction'da oluşur.
2. Admin giriş yapsa bile `/admin` paneline **erişemez** (`canAccessPanel` → `shelter.status` kontrolü).
3. Superadmin, **Admin Onayları** ekranından onaylar/reddeder.
4. Onay → `shelter.status = approved`, `approved_at = now()`.
5. `AdminRegistrationStatusNotification` admin'e gönderilir.
6. Onaylı admin artık `/admin` paneline girer.

### Giriş

Email + şifre, tüm roller için ortak. Giriş sonrası yönlendirme role göre:
- `superadmin` → `/superadmin`
- `admin` (approved) → `/admin`
- `admin` (pending/rejected) → bilgi ekranı ("Barınağınız onay bekliyor / reddedildi")
- `user` → `/` veya `/me`

## Panel Erişimi — `canAccessPanel`

```php
public function canAccessPanel(Panel $panel): bool
{
    return match ($panel->getId()) {
        'superadmin' => $this->role === Role::Superadmin && ! $this->is_banned,
        'admin'      => $this->role === Role::Admin
                        && $this->shelter?->status === ShelterStatus::Approved,
        default      => false,
    };
}
```

## Policy / Gate Haritası

| Policy | Kural özeti |
|---|---|
| `ShelterPolicy` | `update`: admin yalnızca kendi barınağı; superadmin tümü. `approve`/`suspend`: yalnızca superadmin. |
| `AnimalPolicy` | `create`/`update`/`delete`: admin → `animal.shelter_id === user.shelter_id`; superadmin tümü. `view`: herkes (aktif olanlar). |
| `NeedPolicy` | `AnimalPolicy` ile aynı tenant kuralı; tamamlanmış ihtiyaç düzenlenemez. |
| `DonationPolicy` | `create`: kimliği doğrulanmış herkes. `viewAny` (panelde): admin kendi barınağı; superadmin tümü. |
| `AnnouncementPolicy` | `create`/`update`/`delete`: admin kendi barınağı; superadmin tümü. |
| `UserPolicy` | `ban`/`unban`: yalnızca superadmin. `update`: kullanıcı yalnızca kendisi. |

Tenant izolasyonu ayrıca `ShelterScope` global scope ile veri katmanında da uygulanır
(bkz. [01-mimari.md](01-mimari.md) §2) — Policy ve Scope iki kat savunma sağlar.

## Ban Davranışı

`users.is_banned = true` olan kullanıcı:
- Giriş yapamaz (login sonrası kontrol).
- Public profili gizlenir, leaderboard'dan çıkarılır.
- Geçmiş bağışları ve `total_donated` korunur (veri silinmez).
