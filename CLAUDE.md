# ReLife — Proje Kuralları

Çok kiracılı (multi-tenant) hayvan barınağı bağış platformu. Tüm teknik kararlar ve
spesifikasyon `docs/` klasöründedir — çalışmaya başlamadan önce
[docs/README.md](docs/README.md) okunur.

## Stack

- **Laravel 11** — backend framework
- **Livewire 3** — public/donor sayfaları (reaktif bileşenler)
- **Filament 3** — admin + superadmin panelleri (multi-panel)
- **Tailwind CSS + Alpine.js** — stil ve client-side etkileşim
- **Laravel Breeze** (Livewire stack) — user auth
- Veritabanı: MySQL / MariaDB

> `stancl/tenancy` ve `spatie/laravel-permission` **kullanılmaz** (Faz 1). Multi-tenancy
> custom `ShelterScope` global scope ile; roller `users.role` enum + Policy ile yönetilir.

## Kodlama Standartları

- PSR-12. Biçimlendirme: `./vendor/bin/pint` (commit öncesi çalıştır).
- **Domain dili Türkçe, kod sembolleri İngilizce.** Sınıf/metod/değişken/migration adları
  İngilizce (`Shelter`, `Donation`, `collected_amount`); kullanıcıya görünen metinler,
  yorumlar ve dokümanlar Türkçe.
- Para alanları `decimal(12,2)`. Tutar mantığı denormalize alanlara (`collected_amount`,
  `total_donated`, `badge_level`) yalnızca ilgili listener içinde dokunur.
- Test framework: Pest.

## Sprint İş Akışı

Her iş kalemi şu sırayla ilerler:

```
migration → model → enum → policy → action/event/listener → livewire/filament → blade → test
```

Sprint kapsamı ve kabul kriterleri: [docs/06-implementasyon-plani.md](docs/06-implementasyon-plani.md).
Bir sprintin kabul kriterleri karşılanmadan sonrakine geçilmez.

## Klasör Organizasyonu

`app/` altında projeye özel klasörler: `Models/`, `Livewire/`, `Filament/`, `Policies/`,
`Events/`, `Listeners/`, `Notifications/`, `Scopes/`, `Actions/`, `Enums/`. Detay:
[docs/01-mimari.md](docs/01-mimari.md) §6.

## Güvenlik Kuralı

Sahte ödeme ekranı kart verisini **asla** kalıcı hale getirmez — `payment_meta` yalnızca
kart numarasının son 4 hanesini içerir; CVV / tam numara / SKT saklanmaz.

## Yardımcı Komutlar

- `/sprint <no>` — ilgili sprintin görevlerini başlatır
- `/yeni-model <Ad>` — migration + model + factory + policy iskeleti üretir
