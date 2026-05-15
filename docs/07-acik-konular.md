# 07 — Açık Konular ve Karar Önerileri

Bu dosya, spec'te netleştirilmemiş noktaları ve her biri için **benimsenen varsayılanı**
listeler. Aksi belirtilmedikçe implementasyon bu varsayılanlara göre yapılır.

## 1. Karma Anonim / İsimli Bağış

**Soru:** Bir kullanıcı bazı bağışlarını anonim, bazılarını isimli yaparsa profili ve
leaderboard görünümü ne olur?

**Karar:**
- Public profil **erişilebilir kalır**; profil içindeki bağış geçmişinde anonim bağışlar
  "Anonim bağış" olarak, isimli bağışlar normal listelenir.
- Leaderboard'da: ilgili dönemdeki **tüm** bağışları anonimse → "Anonim Bağışçı" (link yok).
  En az bir isimli bağışı varsa → gerçek isim + profil linki.

Detay: [04-is-kurallari.md](04-is-kurallari.md) §4.

## 2. Hayvan Fotoğrafı — Tek mi Galeri mi?

**Karar:** Faz 1'de **tek fotoğraf** (`animals.photo_path`). Faz 2'de
`spatie/laravel-medialibrary` ile galeri.

## 3. Şifre Sıfırlama & Email Doğrulama

**Karar:** Laravel **Breeze** (Livewire stack) built-in özellikleri kullanılır; ayrıca
geliştirme yapılmaz.

## 4. Para Birimi

**Karar:** Faz 1 yalnızca **TRY**. Şema seviyesinde `donations.currency char(3) default 'TRY'`
kolonu hazır tutulur; çoklu para birimi gelecekte UI eklenerek açılabilir.

## 5. Veteriner — Barınak İlişkisi

**Soru:** Bir veteriner birden fazla barınağa bağlanabilir mi?

**Karar:** Faz 2'de netleştirilecek. Ön eğilim: 1 veteriner = 1 barınak (admin gibi
`shelter_id` taşır). Kesin karar [08-faz-2.md](08-faz-2.md) tasarımında verilir.

## 6. Hedef Aşımı (collected > target)

**Karar:** Bağış, ihtiyacı hedefin üzerine taşırsa kabul edilir; iade/bölme yapılmaz.
İhtiyaç `completed` olur. Faz 1 için yeterli.

## 7. Senkron vs Kuyruk (Queue) İşleme

**Karar:** Faz 1'de event listener'ları **senkron** çalışır (basitlik). Bildirim/sertifika
yükü arttığında Faz 2'de `ShouldQueue` ile kuyruğa alınabilir.

## 8. Soft Delete

**Karar:** Bağış kayıtları hiç silinmez. `animals`/`needs` için hard delete yerine
`is_active` / `status` alanları kullanılır; soft delete MVP'de eklenmez.

## Karar Durumu Özeti

| # | Konu | Durum |
|---|---|---|
| 1 | Karma anonim bağış | ✅ Karar verildi |
| 2 | Hayvan fotoğrafı | ✅ Faz 1 tek, Faz 2 galeri |
| 3 | Şifre/email | ✅ Breeze built-in |
| 4 | Para birimi | ✅ Faz 1 TRY sabit |
| 5 | Veteriner-barınak ilişkisi | ⏳ Faz 2'de netleşecek |
| 6 | Hedef aşımı | ✅ Kabul edilir |
| 7 | Senkron/queue | ✅ Faz 1 senkron |
| 8 | Soft delete | ✅ Kullanılmaz |
