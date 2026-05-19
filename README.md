# 🏛️ LayanDesa - Website Pelayanan Publik Desa Berbasis Web

**LayanDesa** adalah website fullstack untuk pelayanan publik desa yang dibangun menggunakan PHP Native, MySQL, HTML, CSS, dan JavaScript.

---

## 📋 Daftar Isi

- [Teknologi](#teknologi)
- [Fitur](#fitur)
- [Struktur Folder](#struktur-folder)
- [Cara Menjalankan](#cara-menjalankan)
- [Akun Admin](#akun-admin)
- [Troubleshooting](#troubleshooting)

---

## 🛠️ Teknologi

| Komponen  | Teknologi         |
|-----------|-------------------|
| Frontend  | HTML5, CSS3, JavaScript (Vanilla) |
| Backend   | PHP Native (≥ 7.4) |
| Database  | MySQL / MariaDB   |
| Web Server| Apache (XAMPP / Laragon) |
| Font      | Plus Jakarta Sans (Google Fonts) |

---

## ✨ Fitur

### 👥 Halaman Publik (Masyarakat)
- **Beranda** – Hero section, statistik desa, berita terbaru, pengumuman, layanan
- **Profil Desa** – Sejarah, visi-misi, struktur organisasi perangkat desa
- **Berita** – Daftar berita dengan pagination + halaman detail
- **Pengumuman** – Daftar pengumuman dengan expand/collapse
- **Layanan** – Info 8 jenis layanan + persyaratan lengkap
- **Kontak & Pengaduan** – Form pengiriman pesan ke database

### 🔐 Halaman Admin
- **Login Admin** – Autentikasi dengan `password_verify()` (bcrypt)
- **Dashboard** – Statistik, berita terbaru, pesan masuk terbaru
- **CRUD Berita** – Tambah, edit, hapus berita
- **CRUD Pengumuman** – Tambah, edit, hapus pengumuman
- **Kotak Pesan** – Lihat pesan, filter belum/sudah dibaca, tandai dibaca, hapus

### 🔒 Keamanan
- **Prepared Statement (PDO)** untuk semua query database
- **password_hash / password_verify** untuk password admin
- **htmlspecialchars** untuk semua output data
- **Session-based** autentikasi admin
- **.htaccess** untuk proteksi folder `/config` dan `/admin`
- Validasi input di sisi server (PHP) dan client (JavaScript)

---

## 📁 Struktur Folder

```
layandesa/
├── index.php               # Halaman utama (Beranda)
├── profil.php              # Profil Desa
├── berita.php              # Daftar Berita
├── berita-detail.php       # Detail Berita
├── pengumuman.php          # Pengumuman
├── layanan.php             # Informasi Layanan
├── kontak.php              # Kontak & Pengaduan
├── generate_password.php   # ⚠️ Hapus setelah dipakai!
│
├── config/
│   ├── database.php        # Koneksi PDO + helper functions
│   └── .htaccess           # Proteksi folder config
│
├── admin/
│   ├── .htaccess           # Proteksi file sensitif admin
│   ├── auth.php            # Guard: cek session login
│   ├── layout.php          # Template sidebar + topbar
│   ├── layout_end.php      # Penutup template
│   ├── login.php           # Halaman login
│   ├── logout.php          # Proses logout
│   ├── dashboard.php       # Dashboard admin
│   ├── berita.php          # Daftar & hapus berita
│   ├── berita-tambah.php   # Form tambah berita
│   ├── berita-edit.php     # Form edit berita
│   ├── pengumuman.php      # Daftar & hapus pengumuman
│   ├── pengumuman-tambah.php  # Form tambah pengumuman
│   ├── pengumuman-edit.php    # Form edit pengumuman
│   └── pesan.php           # Kotak pesan masuk
│
├── includes/
│   ├── header.php          # Navbar + head HTML
│   └── footer.php          # Footer + closing HTML
│
├── assets/
│   ├── css/
│   │   ├── style.css       # Stylesheet halaman publik
│   │   └── admin.css       # Stylesheet panel admin
│   ├── js/
│   │   ├── main.js         # JavaScript halaman publik
│   │   └── admin.js        # JavaScript panel admin
│   └── img/
│       └── favicon.svg     # Ikon website
│
└── database/
    └── layandesa.sql       # Script database + data dummy
```

---

## 🚀 Cara Menjalankan

### Prasyarat
- XAMPP (direkomendasikan) atau Laragon yang sudah terinstall
- PHP versi ≥ 7.4
- MySQL / MariaDB

### Langkah 1 – Menyalin Folder Project
```
Salin folder "layandesa" ke direktori web server:
• XAMPP  : C:\xampp\htdocs\layandesa
• Laragon : C:\laragon\www\layandesa
```

### Langkah 2 – Membuat Database
1. Buka browser, akses `http://localhost/phpmyadmin`
2. Klik tombol **"New"** (baru) untuk membuat database
3. Nama database: `layandesa`, klik **Create**
4. Pilih database `layandesa`, klik tab **Import**
5. Pilih file `layandesa/database/layandesa.sql`
6. Klik tombol **Go** / **Impor**

**Atau via command line:**
```bash
mysql -u root -p < layandesa/database/layandesa.sql
```

### Langkah 3 – Konfigurasi Database (jika perlu)
Buka file `config/database.php` dan sesuaikan:
```php
define('DB_HOST', 'localhost');  // Host database
define('DB_USER', 'root');       // Username MySQL
define('DB_PASS', '');           // Password MySQL (kosong untuk XAMPP default)
define('DB_NAME', 'layandesa');  // Nama database
```

> **Laragon:** Password default biasanya `root` atau kosong

### Langkah 4 – Jalankan Web Server
- **XAMPP**: Start Apache + MySQL dari XAMPP Control Panel
- **Laragon**: Start All Services

### Langkah 5 – Buka di Browser
```
Website Publik : http://localhost/layandesa/
Panel Admin    : http://localhost/layandesa/admin/login.php
```

---

## 🔑 Akun Admin

| Field    | Value       |
|----------|-------------|
| Username | `admin`     |
| Password | `admin123`  |

> **Cara ganti password:**
> 1. Buka `http://localhost/layandesa/generate_password.php`
> 2. Ubah `$password` dengan password baru
> 3. Salin hash yang dihasilkan
> 4. Update ke database: `UPDATE admin SET password = 'HASH_BARU' WHERE username = 'admin';`
> 5. **Segera hapus** file `generate_password.php` setelah selesai!

---

## 🧪 Data Dummy

Database sudah berisi data contoh:

| Tabel       | Jumlah |
|-------------|--------|
| admin       | 1 akun |
| berita      | 4 berita |
| pengumuman  | 4 pengumuman |
| pesan       | 4 pesan |

---

## ⚠️ Troubleshooting

| Masalah | Solusi |
|---------|--------|
| "Gagal Terhubung ke Database" | Pastikan MySQL jalan & database `layandesa` sudah dibuat |
| Halaman admin langsung redirect ke login | Session belum aktif — pastikan cookies diaktifkan di browser |
| Import SQL gagal | Coba import manual tabel per tabel, atau periksa versi MySQL |
| Gambar tidak muncul | Pastikan path `assets/img/` benar |
| Password salah saat login | Jalankan `generate_password.php` untuk generate hash baru |
| Error 500 | Periksa PHP version (minimal 7.4), cek error log Apache |

---

## 📝 Catatan Pengembangan

- Semua query ke database menggunakan **PDO Prepared Statement** untuk keamanan SQL Injection
- Password admin menggunakan **bcrypt** (melalui `password_hash()` dan `password_verify()`)
- Semua output data menggunakan **`htmlspecialchars()`** untuk mencegah XSS
- Form memiliki validasi di sisi server (PHP) dan client (JavaScript)
- Desain responsive menggunakan CSS Grid dan CSS Variables modern

---

## 📞 Informasi Desa (Dummy)

```
Nama Desa : Desa Sukamaju
Kecamatan : Cikaret
Kabupaten : Sukabumi, Jawa Barat
Telepon   : (0266) 123-456
Email     : desa.sukamaju@gmail.com
```

---

*Dibuat dengan ❤️ untuk masyarakat desa Indonesia.*
