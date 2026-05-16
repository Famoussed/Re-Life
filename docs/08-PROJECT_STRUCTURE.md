# 08 — Project Structure

`app/` **tip-öncelikli + modül alt klasörü** düzeniyle organize edilir: her tip klasörünün
içinde modül alt klasörleri bulunur. Amaç 3 kişilik ekibin paralel, çakışmasız geliştirme
yapabilmesidir.

## Modüller

| Modül | Kapsam |
|---|---|
| **Shelter** | Barınak, duyuru, admin kaydı, barınak onayı, admin/superadmin panel ekranları |
| **Animal** | Hayvan ve ihtiyaç (Need) — katalog, public liste/detay, admin yönetimi |
| **Donation** | Bağış, rozet (Badge), bağış event chain, leaderboard, bağış akışı |
| **Account** | Kullanıcı, rol, kimlik doğrulama, kullanıcı profili |
| **Notification** | In-app bildirimler ve bildirim merkezi |

> Modüller monolith içindedir; modüller arası referans serbesttir (örn. `Donation`,
> `Animal` modelini kullanır). Modül = **kod sahipliği** sınırı, deploy sınırı değil.

## Klasör Yerleşimi

```
app/
  Actions/
    Animal/      ApplyNeedProgressAction
    Donation/    CreateDonationAction, RecalculateUserBadgeAction
    Shelter/     ApproveShelterAction
  Enums/
    Account/     Role
    Animal/      AnimalSpecies, Gender, NeedType, NeedStatus
    Shelter/     ShelterStatus
  Events/
    Donation/    DonationCreated
  Http/
    Controllers/                 (Livewire dışı uçlar — şu an boş)
    Middleware/Account/          EnsureUserRole
    Requests/
  Listeners/
    Donation/    UpdateNeedProgressListener, UpdateUserBadgeListener
  Livewire/
    Account/     UserProfile, UserList
    Animal/      AnimalList, AnimalDetail, AnimalManager, NeedManager
    Donation/    DonationFlow, Leaderboard, DonationList, DonorList, BadgeManager
    Notification/ NotificationCenter
    Shelter/     ShelterProfile, RegisterShelterAdmin, AdminDashboard,
                 SuperadminDashboard, AnnouncementManager, ShelterProfileEdit,
                 ShelterApprovals, ShelterList
    Actions/, Forms/             (Breeze auth altyapısı)
  Models/
    Account/     User
    Animal/      Animal, Need
    Donation/    Donation, Badge
    Shelter/     Shelter, Announcement
  Notifications/
    Notification/ NeedCompleted, BadgeEarned, ShelterAnnouncement, AdminRegistrationStatus
  Scopes/
    Shelter/     ShelterScope
  Services/
    Donation/    DonationService
    Shelter/     ShelterService, RegisterShelterAdminService, AnnouncementService

resources/views/livewire/
    account/  animal/  donation/  notification/  shelter/

database/migrations · database/seeders · database/factories
routes/web.php   (public + donor + admin + superadmin grupları)
tests/Feature · tests/Unit
```

## Namespace Kuralı (PSR-4)

Namespace dosya yoluyla birebir eşleşir:

| Dosya | Namespace / Sınıf |
|---|---|
| `app/Services/Donation/DonationService.php` | `App\Services\Donation\DonationService` |
| `app/Models/Animal/Need.php` | `App\Models\Animal\Need` |
| `app/Livewire/Shelter/AdminDashboard.php` | `App\Livewire\Shelter\AdminDashboard` |

## Katman → Klasör Eşlemesi

| Katman | Klasör | Sorumluluk |
|---|---|---|
| Sunum | `Livewire/<Modül>/`, `Http/Controllers/` | UI state, Service çağrısı |
| Validation | Livewire `rules()`, `Http/Requests/` | Girdi doğrulama |
| İş mantığı | `Services/<Modül>/` | Orkestrasyon, transaction, event dispatch |
| Operasyon | `Actions/<Modül>/` | Tek amaçlı, yeniden kullanılabilir |
| Veri | `Models/<Modül>/`, `Scopes/<Modül>/` | Eloquent, ilişki, scope |
| Yan etki | `Events/`, `Listeners/`, `Notifications/` (modül alt klasörlü) | Bildirim, denormalize alan |
| Yetki | `Http/Middleware/Account/` | Authorization |

## İsimlendirme Kuralları

| Tür | Kural | Örnek |
|---|---|---|
| Service | `<Domain>Service` | `DonationService` |
| Action | `<Fiil><Nesne>Action` | `CreateDonationAction` |
| Event | Geçmiş zaman | `DonationCreated` |
| Listener | `<Fiil>...Listener` | `UpdateNeedProgressListener` |
| Notification | `<Olay>Notification` | `BadgeEarnedNotification` |
| Livewire | Modül namespace + PascalCase | `Shelter\AdminDashboard` |

Ayrıntılı kodlama standartları: [14-CODING_STANDARDS.md](./14-CODING_STANDARDS.md).

---

**Sonraki:** [09-SERVICE_ACTION_CATALOG.md](./09-SERVICE_ACTION_CATALOG.md)
