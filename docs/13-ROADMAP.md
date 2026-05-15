# 13 — Roadmap & Open Decisions

Faz 2 kapsamı ve karara bağlanmamış noktalar.

---

## 1. Faz 2 Kapsamı

### Veteriner Rolü
- `users.role` enum'unda `veterinarian` baştan tanımlı.
- Veteriner barınağa bağlı (`shelter_id`). İlişki kardinalitesi: bkz. §2 Açık Konu 5.
- Yetkiler: bağlı barınağın hayvanlarının `health_status` alanını günceller, sertifikaları onaylar.

### Otomatik Sertifika Sistemi
- Her bağış sonrası bağışçıya PDF sertifika üretilir.
- `certificates` tablosu: `user_id`, `donation_id`, `pdf_path`, `veterinarian_id`.
- PDF: `barryvdh/laravel-dompdf`.
- `DonationCreated` event'ine yeni `GenerateCertificateListener` eklenir.
- Sertifika bağış başarı ekranında ve profilde indirilebilir.

### Anasayfa Sertifika / Öne Çıkan Bağışçı
- Anasayfaya "son sertifikalar" / "öne çıkan bağışçılar" bölümü (yalnızca anonim olmayanlar).

### Diğer Faz 2 İyileştirmeleri
| Konu | Açıklama |
|---|---|
| Hayvan foto galerisi | `spatie/laravel-medialibrary` ile çoklu fotoğraf |
| E-posta bildirimi | `notifications` için `mail` kanalı |
| Queue altyapısı | Event listener'ları `ShouldQueue` (yük arttığında) |
| Prod altyapısı | PostgreSQL, S3-uyumlu depolama, Docker — dağıtım aşamasında |

### Faz 2 Ön Sprint Taslağı
| Sprint | Çıktı |
|---|---|
| F2-1 | Veteriner rolü + panel erişimi + sağlık bilgisi güncelleme |
| F2-2 | `certificates` modeli + DomPDF + otomatik üretim |
| F2-3 | Veteriner sertifika onay akışı |
| F2-4 | Anasayfa sertifika bölümü + foto galerisi |

---

## 2. Açık Konular ve Karar Önerileri

Aksi belirtilmedikçe implementasyon bu varsayılanlara göre yapılır.

| # | Konu | Karar |
|---|---|---|
| 1 | Karma anonim/isimli bağış | Profil erişilebilir; anonim bağışlar geçmişte "Anonim bağış" satırı. Leaderboard kuralı: [04-BUSINESS_RULES.md](./04-BUSINESS_RULES.md) §5. |
| 2 | Hayvan fotoğrafı | Faz 1 tek foto; Faz 2 medialibrary galeri. |
| 3 | Şifre sıfırlama / email doğrulama | Laravel Breeze built-in. |
| 4 | Para birimi | Faz 1 yalnızca TRY; `donations.currency` kolonu hazır. |
| 5 | Veteriner-barınak ilişkisi | ⏳ Faz 2'de netleşecek. Ön eğilim: 1 veteriner = 1 barınak. |
| 6 | Hedef aşımı (collected > target) | Kabul edilir; iade/bölme yok. |
| 7 | Event işleme | Faz 1 senkron; queue Faz 2. |
| 8 | Soft delete | Kullanılmaz; `is_active`/`status` alanları ve ban/suspend ile yönetilir. |

---

**Sonraki:** [14-CODING_STANDARDS.md](./14-CODING_STANDARDS.md)
