# 06 — Multi-Tenancy

ReLife çok kiracılı bir platformdur: her barınak (admin) yalnızca kendi verisini görür.

> **Yaklaşım:** Single Database, Shared Schema. `stancl/tenancy` paketi **kullanılmaz** —
> bağımlılığı azaltmak ve lokal sadelik için custom global scope.

---

## 1. Tenant Kolonu

Tenant'a bağlı tüm tablolar `shelter_id` taşır:

- `animals`, `needs`, `donations`, `announcements`

`needs` tablosu `animal_id` üzerinden barınağa zaten ulaşabilir; yine de sorgu kolaylığı
ve scope tutarlılığı için `shelter_id` denormalize edilir.

---

## 2. ShelterScope Global Scope

`App\Scopes\ShelterScope`, tenant modellerine `booted()` içinde `addGlobalScope` ile bağlanır.

```php
class ShelterScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user?->role === Role::Admin && $user->shelter) {
            $builder->where($model->getTable().'.shelter_id', $user->shelter->id);
        }
    }
}
```

| Rol / durum | Scope davranışı |
|---|---|
| `admin` | `where('shelter_id', <admin'in barınağı>)` |
| `superadmin` | Scope uygulanır ama koşul eklenmez → tüm veri |
| `user` / misafir | Koşul eklenmez → tüm public veri |

---

## 3. Scope Bypass

Superadmin panelinde veya global raporlarda bir admin oturumu altında tüm veriye erişmek
gerekirse:

```php
Animal::withoutGlobalScope(ShelterScope::class)->get();
```

Public Livewire bileşenleri admin oturumu olmadan çalıştığından scope tetiklenmez; ek
olarak public sorgular `is_active` / `shelter.status = approved` filtreleri uygular.

---

## 4. İki Kat Savunma

Tenant izolasyonu iki katmanda sağlanır:

1. **Veri katmanı —** `ShelterScope`: admin sorguları otomatik filtrelenir.
2. **Yetki katmanı —** Policy: bir admin başka barınağın kaydını ID ile çağırsa bile
   Policy reddeder (`animal.shelter_id === user.shelter_id`).

Bkz. [05-RBAC_PERMISSIONS.md](./05-RBAC_PERMISSIONS.md) §5.

---

## 5. Bağış Akışında Tenant

Bir donor (hatta donor gibi davranan bir admin) herhangi bir barınağa bağış yapabilir.
Bağışta `shelter_id`, **seçilen hayvan/barınaktan** alınır — `ShelterScope`'tan değil.
Yani bağış akışı tenant-scoped değildir; public veridir.

---

**Sonraki:** [07-PAGES_AND_FLOWS.md](./07-PAGES_AND_FLOWS.md)
