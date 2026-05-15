# ReLife — Proje Kuralları

Çok kiracılı (multi-tenant) hayvan barınağı bağış platformu. Tüm teknik kararlar ve
spesifikasyon `docs/` klasöründedir — çalışmaya başlamadan önce
[docs/00-README.md](docs/00-README.md) okunur.

## Stack & Ortam

- **Laravel 11** + **Livewire 3** + **Tailwind CSS** + **Alpine.js** (TALL stack)
- **Laravel Breeze** (Livewire stack) — session-based auth
- **SQLite** veritabanı, **PHP 8.3**
- Proje **yalnızca lokal ortamda** geliştiriliyor.

> **Kullanılmaz:** Filament, WebSocket/Reverb, Redis, MinIO, Docker, `stancl/tenancy`,
> `spatie/laravel-permission`. Cache/queue `database`/`sync` driver; event'ler senkron.
> Multi-tenancy custom `ShelterScope` global scope ile; roller `users.role` enum + Policy.

## Mimari — Service + Action Katmanlı

Katman sırası **zorunlu**, atlama yasak:

```
Livewire Component → Service → Action → Model
```

- **Livewire bileşeni** incedir: UI state + Service çağrısı. Model/Action/`DB` çağıramaz.
- **Service** iş mantığını orkestrasyon eder: `DB::transaction`, event dispatch.
- **Action** tek amaçlı operasyon (`execute()`), transaction açmaz.
- Admin/superadmin panelleri de düz Livewire bileşenidir (Filament yok).

Ayrıntı: [docs/01-ARCHITECTURE_OVERVIEW.md](docs/01-ARCHITECTURE_OVERVIEW.md).

## Kodlama Standartları

- PSR-12, `declare(strict_types=1)`. Biçimlendirme: `./vendor/bin/pint`.
- **Domain dili Türkçe, kod sembolleri İngilizce.** Sınıf/metod/kolon adları İngilizce;
  kullanıcıya görünen metin, etiket, yorum ve doküman Türkçe.
- Para alanları `decimal(12,2)`. Denormalize alanlar (`collected_amount`,
  `total_donated`, `badge_level`) yalnızca ilgili listener/action içinde güncellenir.
- Test framework: Pest. Her model için factory; yeni iş kuralı → aynı sprintte test.

Tam liste: [docs/14-CODING_STANDARDS.md](docs/14-CODING_STANDARDS.md).

## Sprint İş Akışı

Her iş kalemi şu sırayla ilerler:

```
migration → model → enum → policy → service/action → event/listener → livewire → blade → test
```

Sprint kapsamı ve kabul kriterleri:
[docs/11-IMPLEMENTATION_PLAN.md](docs/11-IMPLEMENTATION_PLAN.md). Bir sprintin kabul
kriterleri karşılanmadan sonrakine geçilmez.

## Güvenlik Kuralı

Sahte ödeme ekranı kart verisini **asla** kalıcı hale getirmez — `payment_meta` yalnızca
kart numarasının son 4 hanesini içerir; CVV / tam numara / SKT saklanmaz.

## Yardımcı Komutlar & Agent'lar

- `/sprint <no>` — ilgili sprintin görevlerini başlatır
- `/yeni-model <Ad>` — migration + model + factory + policy iskeleti üretir
- Agent'lar: `laravel-backend` (veri katmanı), `service-layer` (iş mantığı),
  `livewire-ui` (arayüz + paneller)
