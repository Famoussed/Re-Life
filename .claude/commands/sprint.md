---
description: Belirtilen sprintin görevlerini docs/11'den okuyup uygulamayı başlatır
argument-hint: <sprint numarası, örn. 1>
---

ReLife projesinde **Sprint $ARGUMENTS** kapsamını uygula.

1. `docs/11-IMPLEMENTATION_PLAN.md` dosyasından Sprint $ARGUMENTS bölümünü oku — iş
   kalemlerini ve kabul kriterlerini çıkar.
2. İlgili `docs/` dosyalarını oku (02-DOMAIN_MODEL, 03-DATABASE_SCHEMA, 04-BUSINESS_RULES,
   05-RBAC_PERMISSIONS, 07-PAGES_AND_FLOWS, 09-SERVICE_ACTION_CATALOG).
3. Sprint kapsamı için bir TodoWrite listesi oluştur.
4. Her iş kalemini şu sırayla ilerlet:
   `migration → model → enum → policy → service/action → event/listener → livewire → blade → test`
5. Agent kullanımı:
   - Veri katmanı (migration/model/policy/event) → `laravel-backend`
   - İş mantığı (Service/Action) → `service-layer`
   - Arayüz (Livewire/Blade — public ve panel) → `livewire-ui`
6. Sprint sonunda kabul kriterlerini tek tek doğrula ve sonucu raporla.

Katman kuralı (Livewire → Service → Action → Model) ihlal edilemez. Önceki sprintlerin
kabul kriterleri karşılanmamışsa önce kullanıcıyı uyar.
