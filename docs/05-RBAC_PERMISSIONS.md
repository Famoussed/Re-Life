# 05 — RBAC & Permissions

Roller, yetki matrisi, auth akışları ve Policy haritası.

> Roller `users.role` enum kolonunda tutulur (`App\Enums\Role`). MVP'de
> `spatie/laravel-permission` **kullanılmaz** — yetkilendirme Laravel Policy/Gate ile.

---

## 1. Roller

| Rol | Açıklama | Erişim |
|---|---|---|
| **superadmin** | Platform sahibi | Tüm veri + admin/barınak onayı |
| **admin** | Barınak yöneticisi (1 admin = 1 barınak) | Yalnızca kendi barınağı |
| **user** | Bağışçı (donor) | Public sayfalar + kendi profili |
| **veterinarian** *(Faz 2)* | Barınağa bağlı veteriner | Hayvan sağlık bilgisi + sertifika onayı |

---

## 2. Yetki Matrisi

| Yetenek | superadmin | admin | user | misafir |
|---|:--:|:--:|:--:|:--:|
| Public sayfaları görüntüle | ✓ | ✓ | ✓ | ✓ |
| Bağış yap | ✓ | ✓ | ✓ | ✗ |
| Hayvan/ihtiyaç yönetimi (kendi barınağı) | — | ✓ | ✗ | ✗ |
| Hayvan/ihtiyaç yönetimi (tüm barınaklar) | ✓ | ✗ | ✗ | ✗ |
| Duyuru yayınla (kendi barınağı) | — | ✓ | ✗ | ✗ |
| Barınağın bağışçılarını gör | ✓ | ✓ (kendi) | ✗ | ✗ |
| Admin/barınak onayı | ✓ | ✗ | ✗ | ✗ |
| Barınak askıya al / aktive et | ✓ | ✗ | ✗ | ✗ |
| Kullanıcı ban / unban | ✓ | ✗ | ✗ | ✗ |
| Global istatistikler | ✓ | ✗ | ✗ | ✗ |
| Kendi profilini düzenle | ✓ | ✓ | ✓ | ✗ |

---

## 3. Auth Akışları

### User (donor) kaydı
Standart Breeze: ad-soyad, email, şifre → email doğrulama → giriş. `role = user`.
Onay gerektirmez.

### Admin (barınak) kaydı
`/admin/register` — standart user alanları **+** barınak bilgileri (ad, **ruhsat no**,
şehir, telefon, adres).

1. `RegisterShelterAdminService` tek transaction'da: `users` (role=admin) + `shelters`
   (status=pending) oluşturur.
2. Admin giriş yapsa bile admin paneline **erişemez** (`shelter.status` kontrolü).
3. Superadmin onaylar/reddeder → `AdminRegistrationStatusNotification`.
4. Onaylı admin admin paneline girer.

### Giriş & Yönlendirme
Email + şifre, tüm roller ortak. Giriş sonrası:
- `superadmin` → superadmin paneli
- `admin` (approved) → admin paneli
- `admin` (pending/rejected) → durum bilgi ekranı
- `user` → `/` veya `/me`
- `is_banned` kullanıcı → giriş reddedilir.

---

## 4. Panel Erişimi

Paneller düz Livewire bileşenleridir (Filament yok). Route grupları middleware ile korunur:

| Route grubu | Middleware koşulu |
|---|---|
| `/admin/*` | `auth` + rol `admin` + `shelter.status === approved` |
| `/superadmin/*` | `auth` + rol `superadmin` + `! is_banned` |

Bu kontrol özel bir middleware (`EnsureUserRole` / `EnsureShelterApproved`) ile yapılır.

---

## 5. Policy Haritası

| Policy | Kural özeti |
|---|---|
| `ShelterPolicy` | `update`: admin yalnızca kendi barınağı; superadmin tümü. `approve`/`suspend`: yalnızca superadmin. |
| `AnimalPolicy` | `create`/`update`/`delete`: admin → `animal.shelter_id === user.shelter_id`; superadmin tümü. `view`: herkes (aktif olanlar). |
| `NeedPolicy` | `AnimalPolicy` ile aynı tenant kuralı; `completed` ihtiyaç düzenlenemez. |
| `DonationPolicy` | `create`: kimliği doğrulanmış herkes. `viewAny` (panel): admin kendi barınağı; superadmin tümü. |
| `AnnouncementPolicy` | `create`/`update`/`delete`: admin kendi barınağı; superadmin tümü. |
| `UserPolicy` | `ban`/`unban`: yalnızca superadmin. `update`: kullanıcı yalnızca kendisi. |

Tenant izolasyonu ayrıca `ShelterScope` ile veri katmanında uygulanır — Policy + Scope
iki kat savunma sağlar. Bkz. [06-MULTI_TENANCY.md](./06-MULTI_TENANCY.md).

---

## 6. Ban Davranışı

`users.is_banned = true` olan kullanıcı: giriş yapamaz, public profili gizlenir,
leaderboard'dan çıkarılır. Geçmiş bağışları ve `total_donated` korunur.

---

**Sonraki:** [06-MULTI_TENANCY.md](./06-MULTI_TENANCY.md)
