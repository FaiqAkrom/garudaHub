# Analisis Sistem Legacy Garuda-Hub

Dokumen ini berisi hasil analisis prototipe `garudaHub` (PHP Native) sebagai dasar
perancangan ulang menggunakan Laravel (MVC + Eloquent).

## 1. Gambaran Sistem

Garuda-Hub adalah situs lelang proyek IT: klien memasang proyek, developer menawar,
klien memilih pemenang.

- Peran pengguna: klien, developer, admin
- Fitur: registrasi dan login, pasang proyek (judul, deskripsi, kategori, budget,
  batas lelang), daftar/cari/detail proyek, pengajuan penawaran (harga, durasi,
  proposal), pemilihan pemenang, dasbor per peran, panel admin
- Teknologi: PHP Native, ekstensi `mysqli`, MySQL/MariaDB
- Struktur file: seluruh aplikasi di `src/index.php` (routing lewat `?page=`) dan
  skema di `src/database.sql`

## 2. Masalah pada Arsitektur Kode

| No | Masalah | Dampak |
|----|---------|--------|
| 1 | Seluruh aplikasi berada dalam satu file `index.php` | Sulit dibaca, dirawat, dan diuji |
| 2 | Routing manual lewat parameter `?page=` | Tidak ada pemetaan URL yang jelas, sulit ditambah fitur |
| 3 | Logika bisnis, query SQL, dan HTML bercampur (spaghetti code) **[CEK]** | Tidak ada pemisahan tanggung jawab, kode sulit dipakai ulang |
| 4 | Query ditulis langsung lewat `mysqli` tanpa lapisan model **[CEK]** | Query berulang dan rawan salah |
| 5 | Tidak ada pemisahan Model, View, Controller | Tidak sesuai prinsip MVC |
| 6 | Otorisasi per peran dicek manual di tiap halaman **[CEK]** | Mudah terlewat dan tidak konsisten |
| 7 | Konfigurasi database memakai `root` tanpa password (bawaan) | Hanya aman untuk lokal |
| 8 | Tidak ada pembatasan percobaan login (rate limit) | Rentan serangan tebak password |
| 9 | Tidak ada header keamanan `X-Frame-Options` dan `Content-Security-Policy` | Rentan clickjacking dan XSS |

## 3. Masalah pada Rancangan Database

> **[CEK]** Bagian ini harus dicocokkan dengan `src/database.sql`.
> Hapus poin yang tidak sesuai dan tambahkan temuanmu sendiri.

| No | Masalah | Dampak |
|----|---------|--------|
| 1 | Relasi antar tabel tidak didefinisikan dengan foreign key **[CEK]** | Data yatim (orphan) dan integritas tidak terjaga |
| 2 | Tipe data kolom kurang tepat, misalnya budget/harga bukan `DECIMAL` **[CEK]** | Risiko kesalahan perhitungan |
| 3 | Status proyek/penawaran dan peran pengguna tidak dibatasi nilainya **[CEK]** | Nilai tidak valid bisa masuk |
| 4 | Satu developer bisa menawar berkali-kali pada proyek yang sama **[CEK]** | Data ganda dan membingungkan klien |
| 5 | Tidak ada indeks pada kolom yang sering dicari **[CEK]** | Pencarian lambat saat data besar |
| 6 | Penentuan pemenang tidak dijamin konsisten antara proyek dan penawaran **[CEK]** | Status proyek dan bid bisa tidak sinkron |

## 4. Rancangan Skema Relasional Baru

Tiga tabel inti dengan relasi:

- `users` 1 — N `projects` (klien memiliki banyak proyek)
- `projects` 1 — N `bids` (satu proyek menerima banyak penawaran)
- `users` 1 — N `bids` (developer mengajukan banyak penawaran)

### users
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | |
| name | VARCHAR | |
| email | VARCHAR, UNIQUE | |
| password | VARCHAR | disimpan dalam bentuk hash |
| role | ENUM(client, developer, admin) | default `client` |
| timestamps | | created_at, updated_at |

### projects
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | |
| client_id | BIGINT, FK → users.id | ON DELETE CASCADE |
| title | VARCHAR | |
| description | TEXT | |
| category | VARCHAR | |
| budget | DECIMAL(15,2) | |
| deadline | DATETIME | batas lelang |
| status | ENUM(open, closed, awarded) | default `open`, diindeks |
| timestamps | | |

### bids
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | |
| project_id | BIGINT, FK → projects.id | ON DELETE CASCADE |
| developer_id | BIGINT, FK → users.id | ON DELETE CASCADE |
| price | DECIMAL(15,2) | |
| duration_days | INT UNSIGNED | |
| proposal | TEXT | |
| status | ENUM(pending, won, rejected) | default `pending` |
| timestamps | | |
| (project_id, developer_id) | UNIQUE | satu developer, satu penawaran per proyek |

Status pemenang disimpan pada `bids.status` (bukan kolom di `projects`) agar tidak ada
relasi melingkar antar tabel.

## 5. Rencana Refactoring ke Laravel (MVC)

| Komponen | Legacy | Laravel |
|----------|--------|---------|
| Routing | `?page=` di `index.php` | `routes/web.php` |
| Model | query `mysqli` langsung | Eloquent: `User`, `Project`, `Bid` |
| Controller | logika di `index.php` | `ProjectController`, `BidController`, `DashboardController` |
| View | HTML bercampur PHP | template Blade di `resources/views` |
| Autentikasi | manual | Laravel Breeze + middleware peran |
| Skema database | file SQL | migration |
| Data dummy | `INSERT` di file SQL | Seeder |
| Validasi | manual | Form Request |

## 6. Tahapan Pengerjaan

1. Setup proyek Laravel dan konfigurasi database
2. Migration untuk `users`, `projects`, `bids`
3. Model Eloquent, relasi, dan seeder
4. Autentikasi berperan dan CRUD proyek (MVC)
5. Penawaran, pemilihan pemenang, dasbor, dan dokumentasi