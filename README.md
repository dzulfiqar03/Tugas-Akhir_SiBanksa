# ♻️ SiBanksa — Sistem Informasi Bank Sampah

<p align="center">
  <img src="public/main-logo.svg" alt="SiBanksa Logo" width="120" />
</p>

<p align="center">
  <strong>Platform digital manajemen bank sampah berbasis PWA untuk Perumahan Sidorukun Indah, Gresik</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Vue.js%203-4FC08D?style=flat&logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Inertia.js-9553E9?style=flat&logo=inertia&logoColor=white" alt="Inertia.js" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/PWA-Workbox-5A0FC8?style=flat&logo=pwa&logoColor=white" alt="PWA" />
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License" />
</p>

---

## 📖 Tentang SiBanksa

**SiBanksa** (Sistem Informasi Bank Sampah) adalah aplikasi web progresif (*Progressive Web App*) yang dirancang untuk mendigitalisasi pengelolaan bank sampah di lingkungan perumahan — mencakup pencatatan setoran, transaksi tabungan sampah, penjadwalan pengambilan, hingga verifikasi keanggotaan warga, yang mencakup **8 RT** di Perumahan Sidorukun Indah, Gresik.

Sistem ini dikembangkan sebagai **Tugas Akhir (skripsi)**, dibangun secara solo menggunakan metodologi **Personal Extreme Programming (PXP)**, dengan pengujian yang komprehensif (white-box, black-box, dan usability testing) untuk memastikan kualitas dan keandalan aplikasi.

### 🎯 Masalah yang Diselesaikan

- Pencatatan setoran sampah manual yang rawan human error dan sulit dilacak riwayatnya
- Minimnya transparansi saldo tabungan sampah bagi nasabah/warga
- Tidak adanya sistem penjadwalan terpusat untuk pengambilan sampah per RT
- Sulitnya pengarsipan dokumen dan bukti transaksi bank sampah

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 🔐 **Role-Based Access Control** | Tiga peran utama: **Bank Sampah** (petugas per-RT), **Ketua RW**, dan **Warga/Nasabah**, masing-masing dengan akses dan dashboard berbeda |
| 💰 **Pencatatan Setoran & Saldo** | Nasabah dapat menyetor sampah berdasarkan kategori (daur ulang/non-daur ulang), dengan perhitungan saldo otomatis berdasarkan harga per satuan |
| 📅 **Penjadwalan Pelaksanaan** | Penjadwalan tanggal pengambilan/setoran sampah per RT, dengan validasi anti-bentrok jadwal |
| 📄 **Arsip Dokumen & Bukti (Evidence)** | Upload dan pengelolaan dokumen pendukung serta bukti transaksi |
| 📍 **Geolokasi** | Pelacakan lokasi petugas/nasabah untuk mendukung proses operasional lapangan |
| 🔔 **Push Notifications** | Notifikasi real-time (berbasis Web Push/VAPID) untuk info jadwal dan status transaksi |
| 🤖 **Chatbot** | Asisten virtual untuk membantu warga mendapatkan informasi seputar layanan bank sampah |
| 📊 **Ekspor Laporan PDF** | Ekspor data transaksi dan setoran ke PDF menggunakan pdfMake, lengkap dengan tabel interaktif (DataTables) |
| 📱 **Progressive Web App** | Dapat di-install seperti aplikasi native (Android/iOS/Desktop), mendukung mode offline untuk halaman yang pernah dikunjungi |
| 🏦 **Manajemen Rekening Bank** | Integrasi data rekening bank (Mandiri, BCA, BNI, BRI, CIMB Niaga) untuk proses pencairan saldo |

---

## 🛠️ Tech Stack

**Backend**
- [Laravel](https://laravel.com/) — REST API & business logic
- MySQL — Basis data relasional
- Laravel Sanctum — Autentikasi

**Frontend**
- [Vue 3](https://vuejs.org/) (Composition API)
- [Inertia.js](https://inertiajs.com/) — Menjembatani Laravel & Vue tanpa membangun API terpisah
- Tailwind CSS — Styling responsif

**PWA & Offline**
- [Workbox 7.0.0](https://developer.chrome.com/docs/workbox) — Service worker & caching strategy
- Web App Manifest — Instalasi ke home screen
- Web Push API + VAPID — Notifikasi push
- `vite-plugin-pwa` (strategi `injectManifest`)

**Testing & Quality**
- Pest PHP — Unit testing (white-box, cyclomatic complexity)
- Equivalence Class Partitioning (ECP) — Black-box testing (kaidah Myers)
- System Usability Scale (SUS) — Pengujian usability (skor **85.625** dari 10 responden)

**Deployment**
- Hostinger (shared hosting)
- TWA (Trusted Web Activity) / PWABuilder — Packaging untuk distribusi Android

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────┐      Inertia.js      ┌──────────────────┐
│   Vue 3 (SPA)    │◄────────────────────►│  Laravel Backend  │
│  - Dashboard     │    (JSON + Props)     │  - Controllers    │
│  - Forms         │                       │  - Form Requests  │
│  - Service Worker│                       │  - Eloquent Models│
└────────┬─────────┘                       └─────────┬─────────┘
         │                                            │
   Workbox Cache                                 MySQL Database
   (offline shell)                          (users, sampah, jadwal_
         │                                    pelaksanaan, dll.)
         ▼
┌─────────────────┐
│  Push Service    │
│  (VAPID/Web Push)│
└──────────────────┘
```

### Struktur Peran (Roles)

| Role | Akses |
|---|---|
| **Developer** | Akses penuh sistem (superuser) |
| **Ketua RW** | Monitoring seluruh RT, verifikasi tingkat RW |
| **Bank Sampah** (per RT) | Kelola setoran, jadwal, dan nasabah di RT masing-masing |
| **Nasabah/Warga** | Setor sampah, cek saldo, lihat jadwal, ajukan pencairan |

---

## 🚀 Instalasi & Menjalankan Proyek

### Prasyarat
- PHP >= 8.1
- Composer
- Node.js >= 18 & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone <repository-url>
cd sibanksa

# 2. Install dependensi backend
composer install

# 3. Install dependensi frontend
npm install

# 4. Salin file environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=sibanksa
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Generate VAPID keys untuk push notification (jika belum ada)
php artisan webpush:vapid

# 8. Build assets frontend (termasuk service worker)
npm run build
# atau untuk development:
npm run dev

# 9. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui `http://127.0.0.1:8000`.

### Menjalankan Testing

```bash
# Unit & Feature test (Pest)
php artisan test
```

---

## 📁 Struktur Direktori Utama

```
sibanksa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/          # Form Request validation (Auth, Setoran, dll.)
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── sw.js                  # Service worker (Workbox)
│   └── main-logo.svg
├── resources/
│   ├── js/
│   │   ├── Pages/              # Halaman Inertia/Vue
│   │   └── Components/
│   └── css/
├── routes/
│   └── web.php
└── vite.config.js              # Konfigurasi Vite + PWA plugin
```

---

## 🧪 Metodologi Pengujian

Sistem ini diuji melalui tiga pendekatan untuk memastikan kualitas perangkat lunak:

1. **White-Box Testing** — Analisis cyclomatic complexity dan flow graph, diverifikasi dengan unit test Pest PHP
2. **Black-Box Testing** — Equivalence Class Partitioning (ECP) mengacu pada kaidah Myers
3. **Usability Testing** — System Usability Scale (SUS) dengan 10 responden, memperoleh skor **85.625** (kategori *Excellent*, mengacu pada Nielsen serta Hwang & Salvendy, 2010)

---

## 📚 Dokumentasi Akademik

Proyek ini merupakan bagian dari Tugas Akhir dengan dokumentasi lengkap meliputi:

- Use Case Diagram, Activity Diagram, Sequence Diagram, Class Diagram, ERD, dan PDM
- Estimasi *story point* (skala 1–5, mengacu pada Cohn, 2005)
- Dokumentasi ditulis sesuai kaidah PUEBI dan konvensi akademik Bahasa Indonesia

---

## 🗺️ Roadmap

- [ ] Integrasi laporan analitik untuk Ketua RW
- [ ] Ekspor data ke format Excel
- [ ] Notifikasi WhatsApp Gateway
- [ ] Dashboard statistik lingkungan (dampak pengurangan sampah)

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik (Tugas Akhir). Silakan hubungi penulis untuk penggunaan lebih lanjut.

---

## 👤 Kontributor

Dikembangkan oleh **Muhammad Dzulfiqar** — Mahasiswa Informatika/Sistem Informasi, Telkom University Surabaya.

<p align="center">Made with ♻️ for a cleaner neighborhood</p>
