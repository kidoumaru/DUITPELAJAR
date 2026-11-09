# DuitPelajar

> Aplikasi pencatatan dan pengelolaan keuangan harian yang dirancang khusus untuk pelajar dan mahasiswa Indonesia.

---

## Deskripsi

DuitPelajar adalah aplikasi web berbasis Laravel yang membantu pelajar dan mahasiswa mengelola keuangan pribadi mereka. Pengguna dapat mencatat pemasukan dan pengeluaran, mengatur anggaran bulanan, memantau target tabungan, serta mengakses laporan keuangan visual. Aplikasi ini juga dilengkapi fitur komunikasi dengan admin melalui pusat bantuan.

---

## Fitur Utama

### Pengguna (User)
- **Catat Transaksi** — Rekam pemasukan dan pengeluaran dengan kategori, tanggal, dan metode pembayaran
- **Anggaran Bulanan** — Batasi pengeluaran per kategori atau secara keseluruhan per bulan
- **Target Tabungan** — Buat dan pantau progres menabung untuk tujuan tertentu
- **Laporan Keuangan** — Visualisasi arus kas, distribusi kategori, dan tren pengeluaran harian
- **Kategori Transaksi** — Lihat kategori global dan kelola kategori kustom
- **Pengumuman** — Baca pengumuman resmi dari admin
- **Artikel Edukasi** — Akses konten edukatif seputar keuangan pelajar
- **Pusat Bantuan** — Kirim pesan dan terima balasan dari admin
- **Notifikasi** — Terima notifikasi pengumuman baru dan balasan pesan admin
- **Profil Akun** — Ubah informasi pribadi, foto profil, dan kata sandi

### Administrator
- **Dashboard Admin** — Statistik ringkasan aplikasi dan grafik pertumbuhan pengguna
- **Manajemen Pengguna** — Lihat, aktifkan, nonaktifkan, dan hapus akun pengguna
- **Manajemen Kategori** — Kelola kategori transaksi global (income/expense)
- **Manajemen Pengumuman** — Buat, edit, dan hapus pengumuman
- **Manajemen Pesan** — Balas pesan bantuan dari pengguna
- **Manajemen Artikel** — Buat dan kelola artikel edukasi keuangan
- **Statistik Aplikasi** — Data pertumbuhan pengguna dan aktivitas aplikasi
- **Riwayat Aktivitas** — Log tindakan penting admin (buat/edit/hapus kategori, terbitkan pengumuman)

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | MySQL (production), SQLite (testing) |
| Frontend | Blade Templates, Tailwind CSS v4, Inter Font |
| Charts | Chart.js 4.6 |
| Build Tool | Vite 8 |
| Testing | PHPUnit 12 |
| Code Style | Laravel Pint |

---

## Prasyarat

- PHP 8.3+
- Composer 2.x
- Node.js 20+ & npm
- MySQL 8.0+ (atau MariaDB)
- Laragon / XAMPP / WAMP (Windows) atau server lokal setara

---

## Instalasi

### 1. Clone repository

```bash
git clone <repository-url>
cd dompetpelajar
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Install dependensi JavaScript

```bash
npm install
```

### 4. Salin file environment

```bash
cp .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Konfigurasi database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dompetpelajar
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Jalankan migrasi database

```bash
php artisan migrate
```

### 8. (Opsional) Jalankan seeder untuk data awal

```bash
php artisan db:seed
```

Seeder akan membuat:
- 22 kategori default (8 pemasukan, 14 pengeluaran)
- 1 akun demo pengguna (`pelajar@duitpelajar.id` / `password123`)

### 9. Buat akun admin

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name'      => 'Admin DuitPelajar',
    'email'     => 'admin@duitpelajar.id',
    'password'  => bcrypt('password_aman'),
    'role'      => 'admin',
    'is_active' => true,
]);
```

### 10. Build asset frontend

```bash
npm run build
```

---

## Konfigurasi Environment

Contoh konfigurasi `.env` (tanpa nilai sensitif):

```env
APP_NAME=DuitPelajar
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dompetpelajar
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

MAIL_MAILER=log
```

> **Catatan:** `APP_TIMEZONE=Asia/Jakarta` penting untuk memastikan perbandingan waktu pada pengumuman berjalan benar.

---

## Menjalankan Aplikasi

### Mode Development

```bash
# Terminal 1: PHP dev server
php artisan serve

# Terminal 2: Vite dev server (hot reload)
npm run dev
```

Atau menggunakan perintah terpadu Laravel:

```bash
composer run dev
```

Akses aplikasi di: `http://localhost:8000`

### Mode Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Testing

Jalankan seluruh test suite:

```bash
php artisan test
```

Test menggunakan SQLite in-memory, sehingga tidak mempengaruhi database utama.

```bash
# Jalankan test spesifik
php artisan test --filter=AuthFeatureTest

# Jalankan dengan output ringkas
php artisan test --compact
```

**Cakupan test yang tersedia:**
- `AuthFeatureTest` — Registrasi, login, logout, update profil
- `TransactionFeatureTest` — CRUD transaksi, validasi kategori
- `BudgetFeatureTest` — Set dan hapus anggaran
- `CategoryFeatureTest` — Kategori sistem dan kustom
- `SavingsGoalFeatureTest` — Target tabungan dan setor dana
- `DashboardFeatureTest` — Tampilan dashboard
- `ReportFeatureTest` — Laporan dan ekspor CSV
- `ExampleTest` — Landing page

---

## Struktur Proyek

```
dompetpelajar/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # User & Admin controllers
│   │   └── Middleware/           # Auth, role, single-session
│   └── Models/                   # Eloquent models & enums
├── database/
│   ├── migrations/               # 17 migration files
│   └── seeders/                  # CategorySeeder, DatabaseSeeder
├── resources/
│   └── views/
│       ├── auth/                 # Login, register
│       ├── layouts/              # app.blade.php, admin.blade.php
│       ├── dashboard/
│       ├── transactions/
│       ├── budgets/
│       ├── savings_goals/
│       ├── categories/
│       ├── reports/
│       ├── announcements/
│       ├── articles/
│       ├── messages/
│       ├── notifications/
│       ├── profile/
│       ├── admin/                # Semua halaman admin
│       └── landing.blade.php
├── routes/
│   └── web.php
├── tests/
│   └── Feature/                  # 8 feature test files
├── .env.example
├── composer.json
└── package.json
```

---

## Troubleshooting

**Halaman menampilkan 419 (CSRF Token Mismatch)**
```bash
php artisan config:clear
php artisan cache:clear
```

**Tombol/warna tidak tampil (Tailwind classes)**

Pastikan build sudah dijalankan:
```bash
npm run build
```

**Waktu pengumuman tidak sesuai**

Pastikan `APP_TIMEZONE=Asia/Jakarta` sudah diset di `.env` dan jalankan:
```bash
php artisan config:clear
```

**Sesi sudah tidak aktif setelah login**

Fitur single-session aktif. Login ulang dari tab yang berbeda akan menginvalidasi sesi sebelumnya.

**Error `Route [login] not defined`**

Pastikan route cache bersih:
```bash
php artisan route:clear
```

---

## Lisensi

Proyek ini dibuat untuk keperluan pendidikan dan pengembangan internal.

---

*DuitPelajar — Keuangan cerdas untuk pelajar dan mahasiswa Indonesia.*
