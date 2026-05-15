# 07 — Pages & Flows

Route listesi, sayfalar ve kritik akışlar. Tüm sayfalar **tam-sayfa Livewire bileşeni**
olarak yapılır (Filament yok).

---

## 1. Public Sayfalar (giriş gerektirmez)

| Route | Livewire Bileşeni | Açıklama |
|---|---|---|
| `/` | `Public\AnimalList` | Anasayfa: tüm barınaklardan karışık aktif hayvan kartları. Filtreler: tür, şehir, ihtiyaç tipi. |
| `/animals/{animal}` | `Public\AnimalDetail` | Foto, isim, yaş, cinsiyet, hikaye, sağlık durumu + aktif ihtiyaçlar (progress bar + "Bağış Yap"). |
| `/shelters/{shelter}` | `Public\ShelterProfile` | Barınak bilgileri, hayvan listesi, "Barınağa Genel Destek". |
| `/leaderboard` | `Public\Leaderboard` | Top 100, 3 sekme: Tüm Zamanlar / Bu Yıl / Bu Ay. |
| `/users/{user}` | `Public\UserProfile` | Public profil: isim, rozet, toplam bağış, bağış geçmişi, desteklenen hayvanlar. |
| `/login`, `/register` | Breeze | User giriş / kayıt. |
| `/admin/register` | `Auth\RegisterShelterAdmin` | Admin kayıt formu (ruhsat no dahil) — `pending` barınak. |

Filtreleme (`AnimalList`): yalnızca `is_active` hayvanlar ve `approved` barınaklar;
filtreler URL query string'e yansır (paylaşılabilir link); AND mantığı.

---

## 2. Donor (authenticated) Sayfalar

| Route | Livewire Bileşeni | Açıklama |
|---|---|---|
| `/donate` | `Donation\DonationFlow` | Bağış akışı (§4). |
| `/me` | `Public\UserProfile` (sahip görünümü) | Kendi profili + "Düzenle". |
| `/me/notifications` | `Notification\NotificationCenter` | In-app bildirim merkezi. |

---

## 3. Panel Sayfaları (düz Livewire)

### Admin Paneli `/admin/*` — tenant-scoped

| Route | Bileşen | İşlev |
|---|---|---|
| `/admin` | `Admin\Dashboard` | Toplam bağış (ay/yıl/tüm zaman), aktif hayvan/ihtiyaç sayısı, son 10 bağış, top 10 bağışçı. |
| `/admin/animals` | `Admin\AnimalManager` | Hayvan listesi + oluştur/düzenle/sil + foto upload. |
| `/admin/needs` | `Admin\NeedManager` | İhtiyaç listesi + CRUD; tamamlanan düzenlenemez. |
| `/admin/donations` | `Admin\DonationList` | Liste + filtre (tarih/hayvan/ihtiyaç) + CSV export. |
| `/admin/donors` | `Admin\DonorList` | Barınağa bağış yapanlar + iletişim. |
| `/admin/announcements` | `Admin\AnnouncementManager` | Duyuru CRUD; yayınlama destekçilere bildirim gönderir. |
| `/admin/shelter` | `Admin\ShelterProfileEdit` | Kendi barınak bilgilerini düzenle. |

### Superadmin Paneli `/superadmin/*` — global

| Route | Bileşen | İşlev |
|---|---|---|
| `/superadmin` | `Superadmin\Dashboard` | Global istatistikler. |
| `/superadmin/approvals` | `Superadmin\ShelterApprovals` | `pending` barınaklar → onayla / reddet. |
| `/superadmin/shelters` | `Superadmin\ShelterList` | Tüm barınaklar + askıya al / aktive et. |
| `/superadmin/users` | `Superadmin\UserList` | Tüm kullanıcılar + ban / unban. |
| `/superadmin/badges` | `Superadmin\BadgeManager` | Rozet eşiği düzenleme (opsiyonel). |

> Panel bileşenleri de katman kuralına uyar: Livewire → Service → Action → Model.
> CSV export gibi Livewire dışı uç gerekirse ince bir Controller kullanılır.

---

## 4. Bağış Akışı (`DonationFlow`)

1. **Scope seçimi:** spesifik (hayvan → aktif ihtiyaç) veya barınak genel. `/animals/{id}`
   veya `/shelters/{id}` üzerinden gelindiğinde ön-doldurulur.
2. **Miktar:** hazır tutarlar 50 / 100 / 250 / 500 TL + serbest giriş.
3. **Göstermelik ödeme ekranı** (§5).
4. **"Bağış Yap"** → `DonationService::create()` → başarı ekranı.

---

## 5. Sahte Ödeme Ekranı (kritik)

- Form alanları: Kart Sahibi, Kart Numarası, Son Kullanma (AA/YY), CVV.
- **Gerçek validation yok, hiçbir ödeme servisine bağlanılmaz.**
- "Bağış Yap" tıklandığında:
  1. `donations` kaydı oluşur — `payment_meta` yalnızca kart numarasının **son 4 hanesi**.
     CVV / tam numara / SKT **saklanmaz**.
  2. `DonationCreated` event chain çalışır ([04-BUSINESS_RULES.md](./04-BUSINESS_RULES.md)).
  3. Başarı ekranı; (Faz 2) sertifika linki.

> Bu ekran yalnızca gerçek ödeme deneyimini taklit eder; kart verisi hiçbir biçimde
> kalıcı hale getirilmez.

---

## 6. Route Koruma Özeti

| Grup | Koruma |
|---|---|
| Public | yok |
| Auth ekranları | misafir |
| Donor (`/donate`, `/me*`) | `auth` |
| `/admin/*` | `auth` + rol `admin` + `shelter.status = approved` |
| `/superadmin/*` | `auth` + rol `superadmin` + `! is_banned` |

---

**Sonraki:** [08-PROJECT_STRUCTURE.md](./08-PROJECT_STRUCTURE.md)
