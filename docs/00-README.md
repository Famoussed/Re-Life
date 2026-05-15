# ReLife — Dokümantasyon

Çok kiracılı (multi-tenant) hayvan barınağı bağış platformunun teknik ve iş
dokümantasyonu. Implementasyon kararları bu dosyalardan yürütülür.

## Okuma Sırası

| # | Dosya | Amaç |
|---|---|---|
| 01 | [Architecture Overview](./01-ARCHITECTURE_OVERVIEW.md) | Katmanlı mimari, teknoloji yığını, tasarım kararları |
| 02 | [Domain Model](./02-DOMAIN_MODEL.md) | Entity tanımları, ilişkiler, enum'lar |
| 03 | [Database Schema](./03-DATABASE_SCHEMA.md) | Tablolar, kolonlar, indeksler, migration sırası |
| 04 | [Business Rules](./04-BUSINESS_RULES.md) | Bağış event chain, rozet, leaderboard, kapanma kuralları |
| 05 | [RBAC & Permissions](./05-RBAC_PERMISSIONS.md) | Roller, yetki matrisi, auth akışları, Policy haritası |
| 06 | [Multi-Tenancy](./06-MULTI_TENANCY.md) | ShelterScope global scope stratejisi |
| 07 | [Pages & Flows](./07-PAGES_AND_FLOWS.md) | Route listesi, sayfalar, bağış akışı, sahte ödeme |
| 08 | [Project Structure](./08-PROJECT_STRUCTURE.md) | Klasör organizasyonu, katman eşlemesi |
| 09 | [Service & Action Catalog](./09-SERVICE_ACTION_CATALOG.md) | Service/Action/Event/Listener kataloğu |
| 10 | [Notifications](./10-NOTIFICATIONS.md) | In-app bildirim sistemi |
| 11 | [Implementation Plan](./11-IMPLEMENTATION_PLAN.md) | Sprint bazlı görev kırılımı + kabul kriterleri |
| 12 | [Testing Strategy](./12-TESTING_STRATEGY.md) | Pest test stratejisi |
| 13 | [Roadmap & Open Decisions](./13-ROADMAP.md) | Faz 2 kapsamı + açık konular |
| 14 | [Coding Standards](./14-CODING_STANDARDS.md) | PSR-12, isimlendirme, katman kuralları |

## Hızlı Bakış

- **Proje:** Çok kiracılı hayvan barınağı bağış platformu
- **Stack:** TALL — Tailwind + Alpine.js + Laravel 11 + Livewire 3
- **Mimari:** Service + Action katmanlı mimari (Clean Architecture prensipleri).
  Livewire → Service → Action → Model. **Filament kullanılmaz** — paneller düz Livewire.
- **Ortam:** Yalnızca lokal geliştirme — SQLite, senkron event, lokal dosya. Redis/MinIO/
  Docker/WebSocket **yok**.
- **Roller:** superadmin · admin (barınak) · user (bağışçı) · veterinarian (Faz 2)

## Katkı Kuralları

- Domain dili Türkçe, kod sembolleri İngilizce.
- Katman atlama yasak: Livewire → Service → Action → Model.
- Her sprintte: migration → model → policy → service/action → livewire → blade → test.
- Kod stili: PSR-12, `./vendor/bin/pint`.

Ayrıntı: [14-CODING_STANDARDS.md](./14-CODING_STANDARDS.md).
