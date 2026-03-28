# 🏥 Sistem Informasi Apotek

Sistem Informasi Apotek adalah aplikasi berbasis web yang dirancang untuk memudahkan pengelolaan operasional apotek. Aplikasi ini mencakup manajemen inventaris obat, pencatatan transaksi pembelian (restok) dari vendor, hingga transaksi penjualan kepada pelanggan dengan antarmuka yang modern, cepat, dan ramah pengguna.

Aplikasi ini dibangun menggunakan framework **Laravel** dan menggunakan **Tailwind CSS** untuk tampilan antarmuka penggunanya.

## ✨ Fitur Utama

- **👥 Manajemen Pengguna & Hak Akses**
  - Terdapat pemisahan hak akses (Role) antara **Owner** (Pemilik) dan **Pegawai** (Kasir).
  - Owner dapat mengakses semua menu termasuk manajemen pengguna dan pembelian.
  - Pegawai difokuskan pada transaksi penjualan.
- **📦 Manajemen Master Data**
  - **Jenis Obat:** Pengelolaan kategori obat (Tablet, Sirup, dll) dengan fitur *auto-prefix* untuk pembuatan kode obat otomatis (contoh: TBT-0001).
  - **Stok Obat:** Pemantauan stok secara *real-time*. Stok akan bertambah otomatis saat ada pembelian dan berkurang otomatis saat terjadi penjualan. Terdapat peringatan untuk stok rendah dan obat kedaluwarsa.
- **🛒 Transaksi Pembelian (Inbound)**
  - Pencatatan pembelian obat dari vendor.
  - Penyesuaian Harga Beli (modal) dan input Tanggal Kedaluwarsa (Expiration Date).
- **🛍️ Transaksi Penjualan (Outbound)**
  - Sistem Point of Sales (POS) sederhana untuk mencatat penjualan.
  - Pencarian obat yang sangat cepat menggunakan *Selectize* (bisa dicari berdasarkan kode maupun nama obat).
  - Validasi stok otomatis menggunakan AJAX untuk mencegah penjualan melebihi ketersediaan.
- **📊 Laporan & Ekspor**
  - Fitur ekspor data ke **Excel** untuk Stok Obat, Data Pembelian, dan Data Penjualan.

## 💻 Teknologi yang Digunakan

- **Backend:** [Laravel](https://laravel.com/) (PHP)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, [Tailwind CSS](https://tailwindcss.com/), JavaScript, jQuery
- **Library Tambahan:**
  - [Selectize.js](https://selectize.dev/) (Untuk pencarian *dropdown* obat yang interaktif)
  - [Maatwebsite Excel](https://laravel-excel.com/) (Untuk ekspor data ke Excel)
  - FontAwesome (Untuk ikon antarmuka)

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di mesin lokal Anda.

### Prasyarat
- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL / MariaDB

### Langkah-langkah
1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/username-anda/apotek-information-system.git
   cd apotek-information-system
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Instal dependensi NPM & Build aset:**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database Anda.
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur bagian database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_apotek
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi & Seeder:**
   *(Pastikan database yang diatur di `.env` sudah dibuat)*
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses melalui `http://localhost:8000`.

## 🔐 Kredensial Default (Testing)

Jika Anda menggunakan Seeder, Anda bisa login menggunakan akun bawaan berikut:
- **Owner:** owner@apotek.com / password: `password`
- **Pegawai:** pegawai@apotek.com / password: `password`

*(Catatan: Sesuaikan email di atas jika Anda mendefinisikan email yang berbeda pada `DatabaseSeeder.php` Anda)*

## 📄 Lisensi

Proyek ini bersifat *Open-Source* dan tersedia di bawah Lisensi MIT.
