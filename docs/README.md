# ReLife — Dokümantasyon

Bu klasör, ReLife bağış platformunun tüm teknik ve iş dokümantasyonunu içerir.
Implementasyon kararları bu dosyalardan yürütülür.

## Okuma Sırası

| # | Dosya | Amaç |
|---|---|---|
| 0 | [00-proje-ozeti.md](00-proje-ozeti.md) | Vizyon, hedef kitle, teknoloji stack'i ve paketler |
| 1 | [01-mimari.md](01-mimari.md) | Katmanlı mimari, multi-tenancy, klasör organizasyonu, akış diyagramları |
| 2 | [02-veri-modeli.md](02-veri-modeli.md) | Tüm tablolar, alanlar, ilişkiler, ER diyagramı |
| 3 | [03-roller-yetkiler.md](03-roller-yetkiler.md) | Roller, yetki matrisi, auth akışları, Policy haritası |
| 4 | [04-is-kurallari.md](04-is-kurallari.md) | Bağış event chain, rozet, leaderboard, otomatik kapanma kuralları |
| 5 | [05-sayfalar-akislar.md](05-sayfalar-akislar.md) | Route listesi, sayfalar, sahte ödeme akışı |
| 6 | [06-implementasyon-plani.md](06-implementasyon-plani.md) | Sprint bazlı görev kırılımı + kabul kriterleri |
| 7 | [07-acik-konular.md](07-acik-konular.md) | Karara bağlanmamış noktalar + önerilen varsayılanlar |
| 8 | [08-faz-2.md](08-faz-2.md) | Veteriner rolü, sertifika sistemi, Faz 2 kapsamı |

## Hızlı Bakış

- **Proje:** Çok kiracılı (multi-tenant) hayvan barınağı bağış platformu
- **Stack:** TALL — Tailwind + Alpine.js + Laravel 11 + Livewire 3, panel için Filament 3
- **Roller:** superadmin · admin (barınak) · user (bağışçı) · veterinarian (Faz 2)
- **Faz 1 (MVP):** Auth + multi-tenancy + hayvan/ihtiyaç CRUD + bağış akışı + rozet + leaderboard + paneller
- **Faz 2:** Veteriner rolü + otomatik PDF sertifika

## Katkı Kuralları

- Domain terimleri Türkçe (barınak, bağış, rozet); kod sembolleri İngilizce (`Shelter`, `Donation`, `Badge`).
- Her sprintte sıralama: **migration → model → policy → livewire/filament → blade**.
- Kod stili: PSR-12, `./vendor/bin/pint` ile otomatik biçimlendirme.
