---
description: Belirtilen sprintin görevlerini docs/06'dan okuyup uygulamayı başlatır
argument-hint: <sprint numarası, örn. 1>
---

ReLife projesinde **Sprint $ARGUMENTS** kapsamını uygula.

1. `docs/06-implementasyon-plani.md` dosyasından Sprint $ARGUMENTS bölümünü oku — iş
   kalemlerini ve kabul kriterlerini çıkar.
2. İlgili `docs/` dosyalarını oku (veri modeli, iş kuralları, roller, sayfalar).
3. Sprint kapsamı için bir TodoWrite listesi oluştur.
4. Her iş kalemini şu sırayla ilerlet:
   `migration → model → enum → policy → action/event/listener → livewire/filament → blade → test`
5. Backend işleri için `laravel-backend`, panel işleri için `filament-resource`, public
   arayüz için `livewire-ui` agent'ını kullan.
6. Sprint sonunda kabul kriterlerini tek tek doğrula ve sonucu raporla.

Önceki sprintlerin kabul kriterleri karşılanmamışsa önce kullanıcıyı uyar.
