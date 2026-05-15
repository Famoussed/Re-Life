---
description: Verilen ad için migration + model + factory + policy iskeleti üretir
argument-hint: <ModelAdı, örn. Animal>
---

ReLife projesinde **$ARGUMENTS** için tam bir Eloquent model iskeleti üret.

1. `docs/02-veri-modeli.md` dosyasından $ARGUMENTS tablosunun alanlarını, tiplerini ve
   ilişkilerini çıkar. Tablo yoksa kullanıcıdan şemayı iste.
2. Şunları üret:
   - **Migration** — doğru kolon tipleri, indeksler, FK silme davranışları.
   - **Model** — `$fillable`/`$casts`, ilişkiler, enum cast'leri. Tenant'a bağlı bir
     modelse (`shelter_id` taşıyorsa) `ShelterScope` global scope'unu `booted()` içinde bağla.
   - **Factory** — gerçekçi örnek veri.
   - **Policy** — `docs/03-roller-yetkiler.md` Policy haritasındaki tenant kurallarına göre.
3. Tutar alanları `decimal(12,2)`; kod İngilizce, etiketler Türkçe; PSR-12 / Pint uyumlu.
4. Üretilen dosyaları ve `php artisan migrate` öncesi gereken adımları özetle.

Bu görev için `laravel-backend` agent'ını kullan.
