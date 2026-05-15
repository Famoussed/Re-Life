---
description: Verilen ad için migration + model + factory + policy iskeleti üretir
argument-hint: <ModelAdı, örn. Animal>
---

ReLife projesinde **$ARGUMENTS** için tam bir Eloquent model iskeleti üret.

1. `docs/03-DATABASE_SCHEMA.md` dosyasından $ARGUMENTS tablosunun alanlarını, tiplerini ve
   indekslerini; `docs/02-DOMAIN_MODEL.md`'den ilişkilerini çıkar. Tablo yoksa kullanıcıdan
   şemayı iste.
2. Şunları üret:
   - **Migration** — doğru kolon tipleri, indeksler, FK silme davranışları.
   - **Model** — `$fillable`, `$casts` (enum + `decimal:2`), ilişkiler (açık dönüş tipi).
     Tenant'a bağlıysa (`shelter_id` taşıyorsa) `ShelterScope`'u `booted()` içinde bağla.
   - **Factory** — gerçekçi örnek veri.
   - **Policy** — `docs/05-RBAC_PERMISSIONS.md` Policy haritasındaki tenant kurallarına göre.
3. Tutar alanları `decimal(12,2)`; kod İngilizce, etiketler Türkçe; PSR-12 / Pint uyumlu.
4. Üretilen dosyaları ve `php artisan migrate` öncesi gereken adımları özetle.

Bu görev için `laravel-backend` agent'ını kullan.
