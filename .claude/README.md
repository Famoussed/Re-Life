# .claude/ — Claude Code Yapılandırması

Bu klasör ekip genelinde paylaşılır (git'e dahildir).

## İçerik

| Yol | Amaç |
|---|---|
| `settings.json` | Bash izin allowlist'i (php artisan, composer, npm, pint, pest) |
| `agents/laravel-backend.md` | Migration/model/policy/event üreten subagent |
| `agents/filament-resource.md` | Filament panel kaynağı üreten subagent |
| `agents/livewire-ui.md` | Public Livewire bileşeni + Blade üreten subagent |
| `commands/sprint.md` | `/sprint <no>` — sprint görevlerini başlatır |
| `commands/yeni-model.md` | `/yeni-model <Ad>` — model iskeleti üretir |

## Scaffold Sonrası — Pint Hook'u

Sprint 0'da gerçek Laravel projesi kurulup `vendor/` oluştuktan sonra, düzenlenen PHP
dosyalarını otomatik biçimlendirmek için `settings.json`'a aşağıdaki hook eklenir:

```json
"hooks": {
  "PostToolUse": [
    {
      "matcher": "Edit|Write",
      "hooks": [
        { "type": "command", "command": "./vendor/bin/pint $CLAUDE_FILE_PATHS" }
      ]
    }
  ]
}
```

> Hook, `vendor/bin/pint` mevcut olmadan eklenirse her PHP düzenlemesinde hata verir;
> bu yüzden iskelet aşamasında eklenmez, Sprint 0 sonrasında aktive edilir.
