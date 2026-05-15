
# 01 — Architecture Overview

ReLife'ın genel mimari yapısı, teknoloji yığını ve tasarım kararlarının gerekçeleri.

**İlişkili Dokümanlar:** [Project Structure](./08-PROJECT_STRUCTURE.md) | [Coding Standards](./14-CODING_STANDARDS.md) | [Multi-Tenancy](./06-MULTI_TENANCY.md)

---

## 1. Proje Tanımı

ReLife, hayvan barınaklarını bağışçılarla buluşturan **çok kiracılı (multi-tenant)** bir
bağış platformudur. Barınaklar (admin) kendi hayvanlarını ve ihtiyaçlarını yönetir;
bağışçılar (user) tek hesapla platform genelinde istedikleri hayvana bağış yapar, rozet
kazanır ve global sıralamada yer alır.

---

## 2. Mimari Tanım

> **Service + Action Layered Architecture with Clean Architecture Principles**
>
> Clean Architecture'ın prensiplerini (katman ayrımı, tek yönlü bağımlılık, iş mantığı
> izolasyonu) benimseyen, ancak Laravel'in pragmatik yapısını (Eloquent, FormRequest,
> Livewire validation) framework'e karşı değil framework ile çalışarak uygulayan
> katmanlı mimari.

### Neden "Lightweight Clean Architecture"?

| Uygulanan Prensip | Açıklama |
|---|---|
| Katman ayrımı | Livewire/Controller → Service → Action → Model. Atlanmaz. |
| Tek yönlü bağımlılık | İç katman (Model) dış katmanı (Livewire) asla bilmez |
| İş mantığı izolasyonu | Service/Action HTTP'den bağımsız, direkt test edilebilir |
| Tek sorumluluk | Her katmanın net bir görevi var |

| Strict Clean Arch'tan Bilinçli Sapma | Gerekçe |
|---|---|
| Repository interface yok | DB değişimi planlanmıyor, ekstra soyutlama gereksiz overhead |
| Domain Entity saf PHP değil | Eloquent relationship, scope, cast özelliklerini kaybetmek mantıksız |
| DTO katmanı yok | Livewire `rules()` / `validated()` array olarak yeterli, bu ölçekte overkill |

### Neden Namespace-Based (Modüler Değil)?

- Modüller arası bağımlılık yüksek (Bağış → Hayvan → İhtiyaç → Barınak, Bildirim → hepsi)
- Küçük ekip — modül izolasyonu gereksiz overhead
- Monolith — bağımsız deploy ihtiyacı yok
- Laravel'in doğal namespace yapısıyla uyumlu

---

## 3. Katman Mimarisi

```
┌──────────────────────────────────────────────────────────────┐
│  Livewire Component (tam sayfa)  /  Controller (nadiren)      │
├──────────────────────────────────────────────────────────────┤
│  Validation           │  Livewire rules() veya FormRequest   │
├──────────────────────────────────────────────────────────────┤
│  Livewire Component   │  İNCE — UI state + Service çağrısı   │
│                       │  İş mantığı YASAK                    │
├──────────────────────────────────────────────────────────────┤
│  Service              │  BEYİN — iş mantığı orkestrasyon     │
│                       │  DB::transaction, Event dispatch     │
├──────────────────────────────────────────────────────────────┤
│  Action               │  İŞÇİ — tek amaçlı, yeniden          │
│                       │  kullanılabilir operasyonlar         │
├──────────────────────────────────────────────────────────────┤
│  Model                │  VERİ — Eloquent, Query Scope,       │
│                       │  Relationship, Cast, ShelterScope    │
├──────────────────────────────────────────────────────────────┤
│  Domain Events → Listeners  (Bildirim, denormalize alanlar)  │
└──────────────────────────────────────────────────────────────┘
```

### Request Akışı

```
Livewire Action (örn. "Bağış Yap" butonu)
        ↓
Livewire rules() ile validation
        ↓
Livewire Component metodu (ince)
        ↓
Service::method()
        ↓
  ┌─── DB::transaction ───┐
  │  Action::execute()     │
  │  Event::dispatch()     │
  └────────────────────────┘
        ↓
Livewire re-render / yönlendirme
```

> **Not:** ReLife'ta sayfalar ağırlıklı **tam-sayfa Livewire bileşenleridir**. Controller
> yalnızca Livewire dışı uçlar gerektiğinde (örn. CSV indirme) kullanılır; o durumda
> Controller da yalnızca Service çağırır.

### Katman İletişim Kuralları

| Katman | Amaç | Çağırabilir | Çağıramaz |
|--------|------|-------------|-----------|
| Livewire Component | UI state + Service çağrısı | **Sadece Service** | Model, Action, DB |
| Controller (nadir) | Livewire dışı HTTP | **Sadece Service** | Model, Action, DB |
| Service | İş mantığı orkestrasyon | Action, Model, Event dispatch | Livewire, Controller |
| Action | Tek amaçlı operasyon | Model, diğer Action'lar | Service, Livewire |
| FormRequest / `rules()` | Validation | — | — |
| Policy | Authorization kararı | Model (sorgu) | — |

**Katı Kural:** Katman atlama **YASAK**. Livewire → Service → Action sırası her zaman korunur.

---

## 4. Teknoloji Yığını

Proje şu an **yalnızca lokal ortamda** geliştirilmektedir. Yığın bilinçli olarak sade
tutulmuştur — ekstra altyapı bağımlılığı (Redis, MinIO, Docker, WebSocket) **yoktur**.

| Katman | Teknoloji | Versiyon | Gerekçe |
|--------|-----------|----------|---------|
| Backend Framework | Laravel | 11.x | PHP ekosisteminin en olgun framework'ü |
| Frontend | Livewire | 3.x | SPA-benzeri deneyim, ayrı frontend gerekmez |
| Stil / Etkileşim | Tailwind CSS + Alpine.js | — | TALL stack |
| Veritabanı | SQLite | — | Sıfır konfigürasyon, hızlı `php artisan test` |
| Authentication | Session-based (Laravel Breeze) | — | Livewire ile doğal cookie uyumu |
| Cache / Queue | `database` / `sync` driver | — | Lokal geliştirme için yeterli, ekstra servis yok |
| Dosya Depolama | Lokal `public` disk | — | `storage/app/public` + `php artisan storage:link` |
| PHP | PHP | 8.3 | Enum, typed properties |

### Kapsam Dışı Bırakılanlar (şimdilik)

| Bırakılan | Neden | İleride |
|---|---|---|
| Filament | Katman kuralına (Livewire → Service) uymuyor; paneller düz Livewire ile yapılır | — |
| WebSocket / Laravel Reverb | Gerçek-zaman zorunluluğu yok; bildirimler DB tabanlı | Gerekirse eklenir |
| Redis | Lokal geliştirmede gereksiz; `database`/`sync` driver yeterli | Prod ölçeklemesinde |
| MinIO / S3 | Lokal `public` disk yeterli | Prod'da S3-uyumlu depo |
| Docker | Lokal `php artisan serve` ile çalışılıyor | Prod dağıtımında |
| API / Sanctum | MVP'de harici API tüketici yok | Mobil/3. parti gerekirse |

### Event İşleme

Bağış sonrası iş mantığı (event chain) **senkron** çalışır — listener'lar istek içinde,
aynı transaction kapsamında yürütülür. Kuyruk (queue) altyapısı MVP'de kullanılmaz.
Detay: [04-BUSINESS_RULES.md](./04-BUSINESS_RULES.md).

---

## 5. Mimari Diyagram (Bileşen İlişkileri)

```
┌─────────────────────────────────────────────────────────────────┐
│                       CLIENT (Browser)                          │
│                    Livewire + Alpine.js                         │
└──────────────────────────────┬──────────────────────────────────┘
                               │ HTTP / Livewire
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Laravel Application                          │
│  ┌────────────────────┐  ┌───────────┐  ┌──────────────┐        │
│  │ Livewire Component │  │  Policy   │  │  FormRequest │        │
│  │  (public + panel)  │  └───────────┘  └──────────────┘        │
│  └─────────┬──────────┘                                          │
│            ▼                                                      │
│  ┌────────────────────────┐                                      │
│  │       Services         │◄──── İş mantığı orkestrasyon         │
│  └────────────┬───────────┘                                      │
│               ▼                                                  │
│  ┌────────────────────────┐                                      │
│  │       Actions          │◄──── Tek amaçlı operasyonlar         │
│  └────────────┬───────────┘                                      │
│               ▼                                                  │
│  ┌────────────────────────┐     ┌──────────────┐                 │
│  │    Eloquent Models     │────►│ Domain Events│                 │
│  │  + ShelterScope        │     └──────┬───────┘                 │
│  └────────────┬───────────┘            ▼                         │
│               │                 ┌──────────────┐                 │
│               │                 │  Listeners   │                 │
│               │                 │ (Bildirim,   │                 │
│               │                 │  denormalize │                 │
│               │                 │  alanlar)    │                 │
│               │                 └──────────────┘                 │
└───────────────┼───────────────────────────────────────────────────┘
                ▼
        ┌──────────────┐      ┌──────────────────┐
        │   SQLite     │      │  Lokal Depolama  │
        │     (DB)     │      │ (storage/public) │
        └──────────────┘      └──────────────────┘
```

---

## 6. Cross-Cutting Concerns

| Concern | Çözüm | Detay |
|---------|-------|-------|
| Authentication | Session-based (Breeze) | `middleware('auth')` |
| Authorization | Policy + Middleware + ShelterScope | [05-RBAC_PERMISSIONS.md](./05-RBAC_PERMISSIONS.md) |
| Multi-tenancy | `ShelterScope` global scope | [06-MULTI_TENANCY.md](./06-MULTI_TENANCY.md) |
| Validation | Livewire `rules()` / FormRequest | Her form için tanımlı kurallar |
| In-app Bildirim | Domain Event → Listener → `notifications` tablosu | [10-NOTIFICATIONS.md](./10-NOTIFICATIONS.md) |
| Error Handling | Laravel Exception Handler | Genel try-catch yasak, anlamlı exception'lar |
| File Storage | Lokal `public` disk | Laravel Filesystem abstraction |
| Para tutarları | `decimal(12,2)`, `currency` default `TRY` | [03-DATABASE_SCHEMA.md](./03-DATABASE_SCHEMA.md) |

---

## 7. MVP Kapsamı (Faz 1)

| Modül | MVP | İleride (Faz 2) |
|-------|:---:|:---:|
| Auth — Email/Şifre (3 rol) | ✅ | Veteriner rolü |
| Admin (barınak) kayıt + superadmin onayı | ✅ | — |
| Multi-tenancy (ShelterScope) | ✅ | — |
| Hayvan & İhtiyaç yönetimi (admin) | ✅ | Foto galerisi |
| Filtreli anasayfa + hayvan/barınak detay | ✅ | — |
| Bağış akışı + göstermelik ödeme | ✅ | — |
| Event chain (ihtiyaç ilerlemesi + rozet + bildirim) | ✅ | — |
| Rozet sistemi + Top 100 leaderboard | ✅ | — |
| Kullanıcı profili | ✅ | — |
| Admin & superadmin panelleri (düz Livewire) | ✅ | — |
| Duyurular + in-app bildirim merkezi | ✅ | E-posta bildirimi |
| Otomatik PDF sertifika | ❌ | ✅ |

Detaylı faz/sprint planı: [11-IMPLEMENTATION_PLAN.md](./11-IMPLEMENTATION_PLAN.md) ·
Faz 2: [13-ROADMAP.md](./13-ROADMAP.md).

---

**Sonraki:** [02-DOMAIN_MODEL.md](./02-DOMAIN_MODEL.md) — Entity tanımları ve ilişkiler
