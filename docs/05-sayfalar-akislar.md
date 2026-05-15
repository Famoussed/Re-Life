# 05 — Sayfalar ve Akışlar

## Sayfa Teknolojisi Ayrımı

- **Public + donor sayfaları** → Livewire 3 bileşenleri + Blade + Tailwind + Alpine.
- **Admin & superadmin paneli** → Filament 3 (multi-panel).
- **Auth ekranları** → Laravel Breeze (Livewire stack).

## 1. Public Sayfalar (giriş gerektirmez)

| Route | Bileşen | Açıklama |
|---|---|---|
| `/` | `AnimalList` | Anasayfa: tüm barınaklardan karışık aktif hayvan kartları. Filtreler: tür (kedi/köpek/yavru kedi/yavru köpek), şehir, ihtiyaç tipi. |
| `/animals/{animal}` | `AnimalDetail` | Foto, isim, yaş, cinsiyet, hikaye, sağlık durumu + aktif ihtiyaçlar (her birinde progress bar + "Bağış Yap"). |
| `/shelters/{shelter}` | `ShelterProfile` | Barınak bilgileri, hayvan listesi, "Barınağa Genel Destek" butonu. |
| `/leaderboard` | `Leaderboard` | Top 100, 3 sekme: Tüm Zamanlar / Bu Yıl / Bu Ay. |
| `/users/{user}` | `UserProfile` | Public profil: isim, rozet, toplam bağış, bağış geçmişi, desteklenen hayvanlar galerisi. |
| `/login`, `/register` | Breeze | User giriş / kayıt. |
| `/admin/register` | Breeze (özel) | Admin kayıt formu (ruhsat no dahil) — `pending` barınak oluşturur. |

### Filtreleme (`AnimalList`)

- Filtreler URL query string'e yansır (paylaşılabilir link).
- Yalnızca `is_active = true` hayvanlar ve `approved` barınaklar listelenir.
- Filtre kombinasyonları AND mantığıyla çalışır.

## 2. User (donor) — Authenticated

| Route | Bileşen | Açıklama |
|---|---|---|
| `/donate` | `DonationFlow` | Bağış akışı (aşağıda). |
| `/me` | `UserProfile` (sahip görünümü) | Kendi public profili + "Düzenle" butonu. |
| `/me/notifications` | `NotificationCenter` | In-app bildirim merkezi, okundu işaretleme. |

## 3. Bağış Akışı (`DonationFlow`)

Adımlar:

1. **Scope seçimi**
   - Spesifik: hayvan → o hayvanın aktif ihtiyacı.
   - Barınak genel: barınak seç (`animal_id` ve `need_id` NULL).
   - `/animals/{id}` veya `/shelters/{id}` üzerinden gelindiğinde scope ön-doldurulur.
2. **Miktar:** Hazır tutarlar 50 / 100 / 250 / 500 TL + serbest giriş.
3. **Göstermelik ödeme ekranı** (bkz. §4).
4. **"Bağış Yap"** → `CreateDonationAction` → başarı ekranı.

## 4. Sahte Ödeme Ekranı (kritik akış)

- Form alanları: Kart Sahibi, Kart Numarası, Son Kullanma (AA/YY), CVV.
- **Hiçbir gerçek validation yok, hiçbir ödeme servisine bağlanılmaz.**
- "Bağış Yap" tıklandığında:
  1. `donations` kaydı oluşur — `payment_meta` yalnızca kart numarasının **son 4 hanesi**.
     CVV / tam numara / SKT **saklanmaz**.
  2. `DonationCreated` event chain çalışır (bkz. [04-is-kurallari.md](04-is-kurallari.md)).
  3. Başarı ekranı gösterilir; (Faz 2) sertifika linki eklenir.

> Bu ekran yalnızca gerçek bir ödeme deneyimini taklit eder. Güvenlik açısından kart
> verisi hiçbir biçimde kalıcı hale getirilmez.

## 5. Admin Paneli — Filament `/admin`

Tüm kaynaklar `ShelterScope` ile admin'in barınağına filtrelenir.

| Bölüm | İşlevler |
|---|---|
| **Dashboard** | Toplam bağış (bu ay / bu yıl / tüm zamanlar), aktif hayvan sayısı, aktif ihtiyaç sayısı, son 10 bağış, kendi barınağının top 10 bağışçısı. |
| **Hayvanlar** | CRUD — foto upload + tüm hayvan alanları. |
| **İhtiyaçlar** | CRUD — hayvana bağlı, hedef miktar; tamamlanınca otomatik kapanır, düzenlenemez. |
| **Bağışlar** | Liste + filtre (tarih, hayvan, ihtiyaç) + CSV export. |
| **Bağışçılar** | Barınağa bağış yapmış kullanıcılar + iletişim bilgileri. |
| **Duyurular** | CRUD — yayınlanınca destekçilere in-app bildirim. |
| **Barınak Profili** | Kendi barınak bilgilerini düzenle. |

## 6. Superadmin Paneli — Filament `/superadmin`

`ShelterScope` bypass — global erişim.

| Bölüm | İşlevler |
|---|---|
| **Admin Onayları** | `pending` barınaklar → onayla / reddet. |
| **Tüm Barınaklar** | Liste + askıya al / aktive et. |
| **Tüm Kullanıcılar** | Liste + ban / unban. |
| **Global İstatistikler** | Platform geneli metrikler. |
| **Rozet Tanımları** | Eşik düzenleme (opsiyonel). |

## Route Özet Tablosu

| Grup | Route'lar | Koruma |
|---|---|---|
| Public | `/`, `/animals/{a}`, `/shelters/{s}`, `/leaderboard`, `/users/{u}` | yok |
| Auth | `/login`, `/register`, `/admin/register`, şifre sıfırlama | misafir |
| Donor | `/donate`, `/me`, `/me/notifications` | `auth` |
| Admin paneli | `/admin/*` | `auth` + `canAccessPanel('admin')` |
| Superadmin paneli | `/superadmin/*` | `auth` + `canAccessPanel('superadmin')` |
