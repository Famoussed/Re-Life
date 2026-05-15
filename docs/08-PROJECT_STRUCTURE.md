# 08 — Project Structure

`app/` klasör organizasyonu ve katmanların dosya yerleşimi. Mimari:
[01-ARCHITECTURE_OVERVIEW.md](./01-ARCHITECTURE_OVERVIEW.md).

---

## Klasör Yerleşimi

```
app/
  Actions/            Tek amaçlı operasyonlar (İŞÇİ katman)
    CreateDonationAction.php
    ApproveShelterAction.php
    ...
  Enums/              Role, ShelterStatus, AnimalSpecies, Gender, NeedType, NeedStatus
  Events/             DonationCreated
  Http/
    Controllers/      Yalnızca Livewire dışı uçlar (örn. CSV indirme)
    Middleware/       EnsureUserRole, EnsureShelterApproved
    Requests/         FormRequest (Controller kullanıldığında)
  Listeners/          UpdateNeedProgressListener, UpdateUserBadgeListener,
                      NotifyShelterMetricsListener
  Livewire/
    Public/           AnimalList, AnimalDetail, ShelterProfile, Leaderboard, UserProfile
    Donation/         DonationFlow
    Notification/     NotificationCenter
    Auth/             RegisterShelterAdmin
    Admin/            Dashboard, AnimalManager, NeedManager, DonationList,
                      DonorList, AnnouncementManager, ShelterProfileEdit
    Superadmin/       Dashboard, ShelterApprovals, ShelterList, UserList, BadgeManager
  Models/             User, Shelter, Animal, Need, Donation, Badge, Announcement
  Notifications/      NeedCompletedNotification, BadgeEarnedNotification,
                      ShelterAnnouncementNotification, AdminRegistrationStatusNotification
  Policies/           ShelterPolicy, AnimalPolicy, NeedPolicy, DonationPolicy,
                      AnnouncementPolicy, UserPolicy
  Scopes/             ShelterScope
  Services/           BEYİN katman — iş mantığı orkestrasyon
    DonationService.php
    ShelterService.php
    AnimalService.php
    NeedService.php
    AnnouncementService.php
    RegisterShelterAdminService.php

database/
  migrations/
  seeders/            BadgeSeeder, DemoDataSeeder
  factories/

resources/
  views/
    livewire/         Livewire bileşen blade'leri (Public/, Admin/, Superadmin/ alt klasör)
    components/       Paylaşılan Blade bileşenleri (progress-bar, animal-card, badge)
    layouts/          app, panel
  css/
  js/

routes/
  web.php             Tüm route'lar (public + donor + admin + superadmin grupları)

tests/
  Feature/
  Unit/
```

---

## Katman → Klasör Eşlemesi

| Katman | Klasör | Sorumluluk |
|---|---|---|
| Sunum | `Livewire/`, `Http/Controllers/` | UI state, Service çağrısı |
| Validation | Livewire `rules()`, `Http/Requests/` | Girdi doğrulama |
| İş mantığı | `Services/` | Orkestrasyon, transaction, event dispatch |
| Operasyon | `Actions/` | Tek amaçlı, yeniden kullanılabilir |
| Veri | `Models/`, `Scopes/` | Eloquent, ilişki, scope |
| Yan etki | `Events/`, `Listeners/`, `Notifications/` | Bildirim, denormalize alan güncelleme |
| Yetki | `Policies/`, `Http/Middleware/` | Authorization |

---

## İsimlendirme Kuralları

| Tür | Kural | Örnek |
|---|---|---|
| Service | `<Domain>Service` | `DonationService` |
| Action | `<Fiil><Nesne>Action` | `CreateDonationAction` |
| Event | Geçmiş zaman | `DonationCreated` |
| Listener | `<Fiil>...Listener` | `UpdateNeedProgressListener` |
| Notification | `<Olay>Notification` | `BadgeEarnedNotification` |
| Livewire | Namespace + PascalCase | `Admin\AnimalManager` |
| Policy | `<Model>Policy` | `AnimalPolicy` |

Ayrıntılı kodlama standartları: [14-CODING_STANDARDS.md](./14-CODING_STANDARDS.md).

---

**Sonraki:** [09-SERVICE_ACTION_CATALOG.md](./09-SERVICE_ACTION_CATALOG.md)
