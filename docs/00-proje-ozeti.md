# 00 — Proje Özeti

## Vizyon

ReLife, hayvan barınaklarını bağışçılarla tek bir platformda buluşturan çok kiracılı
(multi-tenant) bir bağış platformudur. Barınaklar kendi hayvanlarını ve hayvanların
spesifik ihtiyaçlarını sisteme ekler; bağışçılar tek hesapla platform genelindeki herhangi
bir barınağın herhangi bir hayvanına/ihtiyacına bağış yapar.

## Hedef Kitle

| Kitle | İhtiyaç |
|---|---|
| **Hayvan barınakları** | Hayvanları ve ihtiyaçları dijital ortamda yönetmek, şeffaf bağış toplamak |
| **Bağışçılar** | Güvenilir, spesifik ve takip edilebilir bağış yapmak, katkılarının görünür olması |
| **Platform sahibi** | Barınakları onaylamak, kötüye kullanımı engellemek, global metrikleri izlemek |

## Temel Değer Önermesi

- **Şeffaflık:** Bağış belirli bir hayvanın belirli bir ihtiyacına (mama / aşı / tedavi) gider; ilerleme çubuğuyla takip edilir.
- **Motivasyon:** Toplam bağışa göre rozet seviyeleri ve global Top 100 sıralama.
- **Tek hesap, çok barınak:** Bağışçı her barınağa ayrı kayıt olmadan bağış yapar.

## Teknoloji Stack'i

**TALL Stack**
- **Tailwind CSS** — utility-first stil
- **Alpine.js** — hafif client-side etkileşim
- **Laravel 11** — backend framework
- **Livewire 3** — public sayfalar için reaktif bileşenler

**Panel:** Filament 3 — admin ve superadmin CRM panelleri (Livewire tabanlı).

## Paketler

| Paket | Sürüm hedefi | Kullanım | Faz |
|---|---|---|---|
| `livewire/livewire` | 3.x | Public reaktif bileşenler | 1 |
| `filament/filament` | 3.x | Admin + superadmin panelleri | 1 |
| `laravel/breeze` | son | User auth (Livewire stack) | 1 |
| `spatie/laravel-medialibrary` | son | Hayvan fotoğraf galerisi | 2 |
| `barryvdh/laravel-dompdf` | son | Sertifika PDF üretimi | 2 |

> **Multi-tenancy:** `stancl/tenancy` paketi **kullanılmaz**. Bunun yerine custom
> `ShelterScope` global scope ile single-database / shared-schema yaklaşımı benimsenir
> (bkz. [01-mimari.md](01-mimari.md)).
>
> **Rol yönetimi:** `spatie/laravel-permission` MVP'de **kullanılmaz**. Roller `users.role`
> enum kolonunda tutulur, yetkilendirme Laravel Policy/Gate ile yapılır.

## Faz Kapsamı

### Faz 1 — MVP
Auth (3 rol) + admin kayıt onay akışı, multi-tenant scope, hayvan & ihtiyaç CRUD,
filtreli anasayfa, bağış akışı + göstermelik ödeme, event chain (ihtiyaç ilerlemesi +
rozet + bildirim), kullanıcı profili, Top 100 leaderboard, Filament admin & superadmin
panelleri, in-app bildirim sistemi.

### Faz 2
Veteriner rolü, otomatik PDF sertifika sistemi, anasayfada sertifika/öne çıkan bağışçı
gösterimi. Ayrıntı: [08-faz-2.md](08-faz-2.md).
