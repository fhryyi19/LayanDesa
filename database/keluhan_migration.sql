-- =============================================
-- Migration: Sistem Keluhan Masyarakat
-- LayanDesa - Desa Sukamaju
-- Jalankan setelah layandesa.sql
-- =============================================

USE layandesa;

-- =============================================
-- Tabel Users (Masyarakat)
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    no_hp VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabel Keluhan
-- =============================================
CREATE TABLE IF NOT EXISTS keluhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kode_tiket VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(255) NOT NULL,
    kategori ENUM('Infrastruktur','Kebersihan','Keamanan','Pelayanan Publik','Kesehatan','Pendidikan','Sosial','Lainnya') NOT NULL DEFAULT 'Lainnya',
    isi TEXT NOT NULL,
    foto_bukti VARCHAR(255) DEFAULT NULL,
    lokasi VARCHAR(255) DEFAULT NULL,
    tanggal_kejadian DATE DEFAULT NULL,
    status ENUM('menunggu','diterima','diproses','selesai','ditolak') NOT NULL DEFAULT 'menunggu',
    alasan_penolakan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_kode_tiket (kode_tiket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabel Tanggapan Keluhan (Timeline)
-- =============================================
CREATE TABLE IF NOT EXISTS tanggapan_keluhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keluhan_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    jenis ENUM('status_update','tanggapan','sistem') NOT NULL DEFAULT 'tanggapan',
    status_baru ENUM('menunggu','diterima','diproses','selesai','ditolak') DEFAULT NULL,
    pesan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (keluhan_id) REFERENCES keluhan(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL,
    INDEX idx_keluhan_id (keluhan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Data Dummy: Users
-- Password: warga123 (hashed)
-- =============================================
INSERT INTO users (nama_lengkap, email, no_hp, password) VALUES
('Budi Santoso', 'budi@email.com', '081234567890', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMeJfg53G2aJkM0lo.fQ4I5K2K'),
('Siti Rahma', 'siti@email.com', '082345678901', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMeJfg53G2aJkM0lo.fQ4I5K2K');

-- =============================================
-- Data Dummy: Keluhan
-- =============================================
INSERT INTO keluhan (user_id, kode_tiket, judul, kategori, isi, lokasi, tanggal_kejadian, status) VALUES
(1, 'KLH-2025-0001', 'Jalan Berlubang di RT 03', 'Infrastruktur', 'Jalan di RT 03 RW 02 sudah rusak parah dan berlubang selama berbulan-bulan. Sangat membahayakan pengendara motor terutama di malam hari saat hujan.', 'Jl. Mawar RT 03 RW 02', '2025-04-09', 'diproses'),
(2, 'KLH-2025-0002', 'Sampah Menumpuk di Pinggir Sungai', 'Kebersihan', 'Ada tumpukan sampah yang besar di pinggir sungai dekat jembatan desa. Sudah berlangsung lebih dari 2 minggu dan menimbulkan bau tidak sedap.', 'Pinggir Sungai dekat Jembatan Desa', '2025-04-06', 'diterima');

-- =============================================
-- Data Dummy: Tanggapan
-- =============================================
INSERT INTO tanggapan_keluhan (keluhan_id, admin_id, jenis, status_baru, pesan) VALUES
(1, 1, 'sistem', 'menunggu', 'Keluhan berhasil dikirim dan menunggu verifikasi admin.'),
(1, 1, 'status_update', 'diterima', 'Keluhan Anda telah diterima dan dicatat oleh petugas desa.'),
(1, 1, 'status_update', 'diproses', 'Tim teknis desa sedang melakukan survei lapangan untuk penanganan jalan rusak ini.'),
(1, 1, 'tanggapan', NULL, 'Kami sudah berkoordinasi dengan Dinas PU Kecamatan. Perbaikan dijadwalkan minggu depan.'),
(2, 1, 'sistem', 'menunggu', 'Keluhan berhasil dikirim dan menunggu verifikasi admin.'),
(2, 1, 'status_update', 'diterima', 'Laporan Anda sudah kami terima. Petugas kebersihan akan segera ditugaskan.');
