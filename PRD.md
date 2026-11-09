# PRD — Product Requirements Document

## DuitPelajar: Aplikasi Pengelolaan Keuangan Harian untuk Pelajar

**Versi:** 1.0  
**Status:** Implemented  
**Tanggal:** November 2026

---

## 1. Project Overview

DuitPelajar adalah aplikasi web berbasis Laravel yang menyediakan platform pencatatan dan pengelolaan keuangan harian khusus untuk pelajar dan mahasiswa Indonesia. Aplikasi ini memungkinkan pengguna mencatat pemasukan dan pengeluaran, mengatur anggaran, memantau target tabungan, serta membaca konten edukatif keuangan.

---

## 2. Latar Belakang Produk

Banyak pelajar dan mahasiswa Indonesia tidak memiliki kebiasaan mencatat keuangan pribadi, sehingga sulit memantau ke mana uang saku mereka pergi. Kurangnya aplikasi keuangan yang dirancang khusus untuk segmen usia pelajar — yang biasanya hanya mengelola uang saku skala kecil — menjadi celah yang perlu diisi.

DuitPelajar dibangun sebagai solusi yang sederhana, ringan, dan gratis tanpa fitur perbankan atau investasi yang rumit.

---

## 3. Problem Statement

- Pelajar kesulitan melacak pengeluaran harian secara konsisten
- Tidak ada tools keuangan sederhana yang disesuaikan untuk skala uang saku pelajar
- Tidak ada mekanisme pengingat atau laporan yang mudah dipahami oleh remaja
- Kurangnya konten edukasi keuangan yang mudah diakses

---

## 4. Visi Produk

Menjadi platform keuangan digital pertama pilihan pelajar dan mahasiswa Indonesia — mudah digunakan, gratis, dan membantu membangun kebiasaan finansial yang sehat sejak dini.

---

## 5. Tujuan Produk

1. Memudahkan pelajar mencatat setiap transaksi keuangan harian
2. Membantu pelajar mengatur anggaran bulanan per kategori
3. Mendorong kebiasaan menabung melalui fitur target tabungan
4. Menyediakan laporan keuangan visual yang mudah dipahami
5. Menyediakan konten edukasi keuangan melalui artikel
6. Memfasilitasi komunikasi antara pengguna dan admin melalui pusat bantuan

---

## 6. Target Pengguna

| Segmen | Deskripsi |
|---|---|
| Siswa SMP | Usia 12–15 tahun, mengelola uang saku harian |
| Siswa SMA/SMK | Usia 15–18 tahun, mulai mengenal pengelolaan anggaran |
| Mahasiswa | Usia 18–24 tahun, pengeluaran lebih kompleks (kos, transportasi, dll.) |

Jenjang pendidikan disimpan di profil pengguna: `SMP`, `SMA/SMK`, `Mahasiswa`.

---

## 7. Role dan Hak Akses

### 7.1 User (Pengguna Reguler)

Akun dengan `role = 'user'`. Dapat mengakses semua halaman di bawah path `/` (non-admin).

| Hak Akses | Detail |
|---|---|
| Transaksi | CRUD transaksi miliknya sendiri |
| Kategori | Lihat semua kategori global; buat/edit/hapus kategori kustom miliknya |
| Anggaran | Set anggaran keseluruhan dan per kategori |
| Tabungan | CRUD target tabungan; setor dana |
| Laporan | Lihat dan ekspor laporan keuangan pribadi |
| Pengumuman | Lihat pengumuman dengan status `published` |
| Artikel | Lihat artikel dengan status `published` |
| Pesan | Kirim pesan ke admin; balas thread sendiri |
| Notifikasi | Terima notifikasi pengumuman baru dan balasan admin |
| Profil | Ubah nama, jenjang pendidikan, foto profil, dan kata sandi |

**Batasan User:**
- Tidak dapat mengakses path `/admin/*`
- Tidak dapat melihat transaksi atau data pengguna lain
- Tidak dapat mengubah kategori sistem (`is_system = true`)
- Tidak dapat mengubah email (hanya dapat dilihat)

### 7.2 Admin

Akun dengan `role = 'admin'`. Hanya dapat mengakses halaman admin (`/admin/*`). Jika admin mencoba akses halaman user, diarahkan ke `/admin/dashboard`.

| Hak Akses | Detail |
|---|---|
| Pengguna | Lihat daftar, aktifkan, nonaktifkan (hanya non-admin), hapus (hanya non-admin) |
| Kategori | CRUD kategori global dengan pencatatan aktivitas |
| Pengumuman | CRUD pengumuman; terbitkan notifikasi ke semua user |
| Pesan | Balas pesan user; tandai selesai |
| Artikel | CRUD artikel dengan upload thumbnail |
| Statistik | Lihat statistik pertumbuhan pengguna |
| Riwayat Aktivitas | Lihat log aktivitas admin |
| Profil Admin | Ubah nama, email, foto profil, dan kata sandi |

**Batasan Admin:**
- Tidak dapat menonaktifkan atau menghapus akun admin lain
- Tidak dapat menonaktifkan atau menghapus akunnya sendiri

---

## 8. Functional Requirements

### FR-01: Autentikasi

| ID | Requirement | Status |
|---|---|---|
| FR-01.1 | Pengguna dapat mendaftar dengan nama, email, jenjang pendidikan, dan kata sandi | ✅ Implemented |
| FR-01.2 | Pengguna dapat login dengan email dan kata sandi | ✅ Implemented |
| FR-01.3 | Akun yang dinonaktifkan tidak dapat login | ✅ Implemented |
| FR-01.4 | Hanya satu sesi aktif per akun pada satu waktu | ✅ Implemented |
| FR-01.5 | Redirect pasca-login berdasarkan role (admin → `/admin/dashboard`, user → `/dashboard`) | ✅ Implemented |
| FR-01.6 | Hanya satu tab browser yang dapat aktif per sesi (client-side enforcement) | ✅ Implemented |

### FR-02: Transaksi

| ID | Requirement | Status |
|---|---|---|
| FR-02.1 | User dapat mencatat transaksi dengan tipe income/expense, nominal, kategori, tanggal, dan catatan | ✅ Implemented |
| FR-02.2 | Kategori income hanya dapat digunakan untuk transaksi income; kategori expense hanya untuk expense | ✅ Implemented |
| FR-02.3 | User dapat mengedit transaksi miliknya | ✅ Implemented |
| FR-02.4 | User dapat menghapus transaksi miliknya | ✅ Implemented |
| FR-02.5 | User dapat memfilter transaksi berdasarkan tipe, kategori, rentang tanggal, dan kata kunci | ✅ Implemented |
| FR-02.6 | User dapat mengekspor data transaksi ke file CSV | ✅ Implemented |

### FR-03: Kategori

| ID | Requirement | Status |
|---|---|---|
| FR-03.1 | Admin dapat membuat, mengedit, mengaktifkan/menonaktifkan, dan menghapus kategori global | ✅ Implemented |
| FR-03.2 | Kategori yang masih digunakan transaksi tidak dapat dihapus | ✅ Implemented |
| FR-03.3 | User dapat membuat kategori kustom untuk dirinya sendiri | ✅ Implemented |
| FR-03.4 | User tidak dapat mengubah atau menghapus kategori sistem | ✅ Implemented |

### FR-04: Anggaran

| ID | Requirement | Status |
|---|---|---|
| FR-04.1 | User dapat menetapkan anggaran keseluruhan per bulan | ✅ Implemented |
| FR-04.2 | User dapat menetapkan anggaran per kategori expense per bulan | ✅ Implemented |
| FR-04.3 | Sistem menampilkan persentase penggunaan anggaran dan peringatan di ≥75% | ✅ Implemented |
| FR-04.4 | User dapat menghapus anggaran yang telah dibuat | ✅ Implemented |

### FR-05: Target Tabungan

| ID | Requirement | Status |
|---|---|---|
| FR-05.1 | User dapat membuat target tabungan dengan nama, nominal target, dan target tanggal | ✅ Implemented |
| FR-05.2 | User dapat menyetor dana ke target tabungan | ✅ Implemented |
| FR-05.3 | Status target otomatis berubah menjadi `achieved` saat dana terkumpul mencapai target | ✅ Implemented |
| FR-05.4 | User dapat mengedit dan menghapus target tabungan | ✅ Implemented |

### FR-06: Laporan Keuangan

| ID | Requirement | Status |
|---|---|---|
| FR-06.1 | User dapat melihat laporan dengan filter preset (bulan ini, minggu ini, tahun ini, kustom) | ✅ Implemented |
| FR-06.2 | Laporan menampilkan grafik batang perbandingan income vs expense | ✅ Implemented |
| FR-06.3 | Laporan menampilkan distribusi pengeluaran per kategori (doughnut chart) | ✅ Implemented |
| FR-06.4 | User dapat mengekspor laporan ke CSV | ✅ Implemented |

### FR-07: Pengumuman dan Artikel

| ID | Requirement | Status |
|---|---|---|
| FR-07.1 | Admin dapat membuat, mengedit, dan menghapus pengumuman | ✅ Implemented |
| FR-07.2 | User hanya dapat melihat pengumuman berstatus `published` dengan `published_at ≤ now()` | ✅ Implemented |
| FR-07.3 | Admin dapat membuat artikel dengan konten, kategori, dan thumbnail opsional | ✅ Implemented |
| FR-07.4 | Saat pengumuman diterbitkan, notifikasi dikirim ke semua user aktif | ✅ Implemented |

### FR-08: Pusat Bantuan

| ID | Requirement | Status |
|---|---|---|
| FR-08.1 | User dapat mengirim pesan ke admin | ✅ Implemented |
| FR-08.2 | Admin dapat membalas pesan dan mengubah status menjadi `processing` | ✅ Implemented |
| FR-08.3 | Admin dapat menandai percakapan sebagai `done` | ✅ Implemented |
| FR-08.4 | User menerima notifikasi saat admin membalas pesannya | ✅ Implemented |

### FR-09: Notifikasi

| ID | Requirement | Status |
|---|---|---|
| FR-09.1 | User menerima notifikasi saat ada pengumuman baru yang diterbitkan | ✅ Implemented |
| FR-09.2 | User menerima notifikasi saat admin membalas pesan | ✅ Implemented |
| FR-09.3 | Notifikasi memiliki status baca/belum baca | ✅ Implemented |
| FR-09.4 | User dapat menandai semua notifikasi sebagai sudah dibaca | ✅ Implemented |

### FR-10: Profil dan Foto

| ID | Requirement | Status |
|---|---|---|
| FR-10.1 | User dapat mengubah nama dan jenjang pendidikan | ✅ Implemented |
| FR-10.2 | User dapat mengupload foto profil (JPEG/PNG/WebP, maks. 2MB) | ✅ Implemented |
| FR-10.3 | User dapat mengubah kata sandi dengan verifikasi kata sandi lama | ✅ Implemented |

---

## 9. Non-Functional Requirements

| ID | Requirement | Detail |
|---|---|---|
| NFR-01 | Keamanan sesi | Single active session per user; sesi lama dihapus saat login baru |
| NFR-02 | Role isolation | Middleware `EnsureUserIsAdmin` dan `EnsureUserIsNotAdmin` mencegah cross-role access |
| NFR-03 | CSRF protection | Semua form menggunakan Laravel CSRF token |
| NFR-04 | Validasi input | Semua input divalidasi di backend sebelum diproses |
| NFR-05 | Foto profil | Validasi MIME, ukuran maks., dan dimensi minimum |
| NFR-06 | Single tab | Client-side localStorage heartbeat mencegah multi-tab |
| NFR-07 | Timezone | `APP_TIMEZONE=Asia/Jakarta` memastikan konsistensi waktu |
| NFR-08 | Responsif | Tampilan mendukung desktop, tablet, dan smartphone |

---

## 10. Spesifikasi Fitur

### 10.1 Sistem Autentikasi Single-Session

Setiap login menyimpan `active_session_id` di tabel `users`. Middleware `EnsureSingleLoginSession` memverifikasi setiap request. Jika tidak cocok, user diarahkan ke halaman login. Login baru otomatis menghapus semua sesi lama dari tabel `sessions`.

### 10.2 Validasi Tipe Kategori–Transaksi

Backend memvalidasi bahwa kategori yang dipilih memiliki `type` yang sama dengan tipe transaksi. Kategori income tidak dapat digunakan untuk transaksi expense, dan sebaliknya.

### 10.3 Proteksi Akun Admin

- Admin tidak dapat menonaktifkan atau menghapus akun dengan `role = 'admin'`
- Ini mencakup dirinya sendiri

### 10.4 Sistem Notifikasi

Notifikasi disimpan di tabel `user_notifications` dengan kolom `type` (announcement_new / message_reply), `url`, dan `read_at`. Dibuat secara batch saat pengumuman diterbitkan atau saat admin membalas pesan.

### 10.5 Riwayat Aktivitas Admin

Tindakan penting admin (create/update/activate/deactivate/delete kategori, publish/update/delete pengumuman) dicatat otomatis di tabel `admin_activity_logs`.

---

## 11. User Flow

### Alur Pengguna Baru

```
Landing Page → Daftar → Login → Dashboard
→ Catat Transaksi → Lihat Laporan
→ Set Anggaran → Pantau Target Tabungan
```

### Alur Admin

```
Login → Admin Dashboard → Kelola Pengguna/Kategori/Pengumuman/Artikel/Pesan
→ Lihat Statistik & Riwayat Aktivitas
```

### Alur Notifikasi

```
Admin terbitkan pengumuman
→ Sistem kirim notifikasi batch ke semua user aktif
→ User lihat badge di navbar
→ User buka halaman Notifikasi → klik "Lihat detail" → marked as read
```

---

## 12. Arsitektur Sistem

```
Browser (Client)
    │
    ├── Vite Assets (CSS/JS) ── Tailwind CSS v4, Chart.js
    │
    └── HTTP Request
           │
           ▼
    Laravel 13 Application
           │
    ├── routes/web.php (single route file)
    │       │
    │       ├── Guest routes (middleware: guest)
    │       ├── Shared auth (middleware: auth + EnsureSingleLoginSession)
    │       ├── User routes (+ EnsureUserIsNotAdmin)
    │       └── Admin routes (prefix: /admin, + EnsureUserIsAdmin)
    │
    ├── Middleware Layer
    │       ├── EnsureSingleLoginSession
    │       ├── EnsureUserIsAdmin
    │       └── EnsureUserIsNotAdmin
    │
    ├── Controllers → Models → Eloquent ORM
    │
    └── Database (MySQL)
```

---

## 13. Ikhtisar Database

| Tabel | Deskripsi |
|---|---|
| `users` | Akun pengguna (role: admin/user, is_active, photo, active_session_id) |
| `categories` | Kategori transaksi (type: income/expense, is_system, is_active) |
| `transactions` | Catatan transaksi (type, amount, date, category_id, payment_method) |
| `budgets` | Anggaran bulanan (category_id nullable = keseluruhan) |
| `savings_goals` | Target tabungan (target_amount, current_amount, status) |
| `messages` | Pesan bantuan pengguna ke admin |
| `message_replies` | Balasan pesan (sender_id = user atau admin) |
| `announcements` | Pengumuman (status: published/draft, published_at) |
| `articles` | Artikel edukasi (thumbnail, slug, status: published/draft) |
| `user_notifications` | Notifikasi in-app untuk user (read_at) |
| `admin_activity_logs` | Log aktivitas admin |
| `sessions` | Data sesi Laravel (driver: database) |
| `cache` | Cache Laravel |

---

## 14. Business Rules

1. **Satu sesi aktif per user** — Login baru menginvalidasi sesi lama
2. **Admin tidak dapat memodifikasi sesama admin** — Termasuk deactivate dan delete
3. **Kategori terhubung transaksi tidak dapat dihapus** — Cek relasi sebelum delete
4. **Tipe kategori harus sesuai tipe transaksi** — income↔income, expense↔expense
5. **Pengumuman hanya tampil jika published DAN published_at ≤ now()** — Mendukung scheduled publishing
6. **Foto profil** — Hanya JPEG/JPG/PNG/WebP, maks. 2MB, min. dimensi 50×50px
7. **Anggaran kategori** — Hanya kategori expense yang dapat digunakan untuk anggaran

---

## 15. Persyaratan Keamanan

| Area | Implementasi |
|---|---|
| Autentikasi | Laravel built-in Auth dengan hash bcrypt |
| Otorisasi | Middleware berbasis role di semua route |
| CSRF | Laravel CSRF token di semua form |
| Session | Single active session, database driver |
| Upload file | Validasi MIME, ukuran, dimensi |
| SQL Injection | Eloquent ORM dengan parameterized queries |
| XSS | Blade auto-escaping (`{{ }}`) untuk semua output user |
| Kepemilikan data | Validasi `user_id` di semua aksi CRUD data pribadi |

---

## 16. Batasan dan Keterbatasan

- Aplikasi hanya tersedia sebagai web app (belum ada mobile app native)
- Tidak ada fitur lupa kata sandi (reset password via email)
- Tidak ada notifikasi real-time (polling/push); notifikasi dikirim saat aksi admin terjadi
- Tidak ada fitur multi-currency; hanya Rupiah (IDR)
- Kategori kustom milik user (bukan kategori sistem) tidak mendukung penggantian ikon/warna melalui antarmuka saat ini
- Tidak ada fitur backup/export data akun secara keseluruhan

---

## 17. Pengembangan Masa Depan (Potensi)

| Fitur | Prioritas |
|---|---|
| Reset kata sandi via email | Tinggi |
| Notifikasi real-time (pusher/WebSocket) | Sedang |
| Fitur kustom ikon dan warna untuk kategori user | Sedang |
| Pengingat (reminder) anggaran via notifikasi | Sedang |
| Ekspor data lengkap akun | Rendah |
| Mobile app (PWA atau native) | Rendah |
| Multi-currency support | Rendah |
| Integrasi kalender akademik | Rendah |

---

*Dokumen ini mencerminkan kondisi aktual implementasi DuitPelajar per November 2026.*
