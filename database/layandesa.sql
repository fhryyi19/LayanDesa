-- =============================================
-- Script Database LayanDesa
-- Website Pelayanan Publik Desa Berbasis Web
-- =============================================

CREATE DATABASE IF NOT EXISTS layandesa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE layandesa;

-- =============================================
-- Tabel Admin
-- =============================================
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabel Berita
-- =============================================
CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabel Pengumuman
-- =============================================
CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabel Pesan / Pengaduan
-- =============================================
CREATE TABLE IF NOT EXISTS pesan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subjek VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- =============================================
-- Data Dummy: Admin
-- Password: admin123 (hashed with password_hash)
-- =============================================
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Desa');

-- =============================================
-- Data Dummy: Berita
-- =============================================
INSERT INTO berita (judul, isi, tanggal) VALUES
('Pelaksanaan Posyandu Rutin Bulan April 2025', 
'Dalam rangka meningkatkan kesehatan masyarakat, Desa Sukamaju kembali mengadakan kegiatan Posyandu rutin pada bulan April 2025. Kegiatan ini dilaksanakan setiap minggu pertama bulan berjalan di Balai Desa.\n\nKegiatan Posyandu mencakup penimbangan bayi dan balita, pemberian vitamin, imunisasi, serta konsultasi gizi gratis bagi ibu hamil dan menyusui. Masyarakat diharapkan dapat memanfaatkan layanan ini sebaik mungkin demi menjaga kesehatan keluarga.\n\nSeluruh warga desa diundang untuk berpartisipasi aktif dalam program ini. Untuk informasi lebih lanjut, silakan menghubungi kantor desa.',
'2025-04-05'),

('Pembangunan Jalan Desa Selesai Dilaksanakan', 
'Pemerintah Desa Sukamaju dengan bangga mengumumkan bahwa proyek pembangunan jalan desa sepanjang 2,5 kilometer telah berhasil diselesaikan tepat waktu. Pembangunan ini menggunakan anggaran Dana Desa tahun 2025 sebesar Rp 450 juta.\n\nJalan yang menghubungkan Dusun Cikaret dengan Dusun Mekarjaya ini kini sudah beraspal dan dilengkapi dengan saluran drainase yang memadai. Diharapkan dengan selesainya pembangunan ini, akses masyarakat menjadi lebih mudah dan meningkatkan perekonomian warga sekitar.\n\nKepala Desa menyampaikan ucapan terima kasih kepada seluruh warga yang telah mendukung proses pembangunan ini.',
'2025-04-02'),

('Program Bantuan Pupuk Subsidi untuk Petani Desa', 
'Sebagai bentuk kepedulian pemerintah daerah terhadap petani, Desa Sukamaju mendapatkan alokasi pupuk subsidi untuk musim tanam tahun 2025. Total pupuk yang tersedia sebesar 50 ton untuk seluruh petani yang terdaftar dalam kelompok tani.\n\nPetani yang berhak menerima bantuan adalah mereka yang telah mendaftarkan diri ke kantor desa dan memiliki lahan pertanian aktif. Distribusi pupuk akan dilakukan mulai tanggal 15 April 2025 di gudang kelompok tani masing-masing.\n\nUntuk pendaftaran dan informasi lebih lanjut, silakan datang langsung ke kantor desa dengan membawa KTP dan dokumen kepemilikan lahan.',
'2025-03-28'),

('Pelatihan Keterampilan Digital bagi Pemuda Desa', 
'Pemerintah Desa Sukamaju bekerja sama dengan Dinas Komunikasi dan Informatika Kabupaten mengadakan pelatihan keterampilan digital bagi pemuda desa. Pelatihan ini bertujuan untuk meningkatkan kompetensi digital generasi muda agar siap menghadapi era industri 4.0.\n\nMateri pelatihan mencakup penggunaan media sosial untuk bisnis, pembuatan konten kreatif, pemasaran digital (digital marketing), dan dasar-dasar e-commerce. Pelatihan dilaksanakan selama 3 hari, yaitu tanggal 20-22 April 2025 di Balai Desa.\n\nPendaftaran dibuka untuk pemuda desa usia 17-35 tahun. Segera daftarkan diri Anda ke kantor desa sebelum kuota penuh!',
'2025-03-25');

-- =============================================
-- Data Dummy: Pengumuman
-- =============================================
INSERT INTO pengumuman (judul, isi, tanggal) VALUES
('Jadwal Pembayaran PBB Tahun 2025', 
'Diberitahukan kepada seluruh warga Desa Sukamaju bahwa pembayaran Pajak Bumi dan Bangunan (PBB) Tahun 2025 telah dibuka. Pembayaran dapat dilakukan di kantor desa setiap hari kerja (Senin-Jumat) pukul 08.00-14.00 WIB.\n\nBatas waktu pembayaran PBB adalah tanggal 30 September 2025. Warga yang terlambat membayar akan dikenakan denda sesuai peraturan yang berlaku. Harap bawa SPPT PBB asli saat melakukan pembayaran.',
'2025-04-10'),

('Pengumuman Penerimaan Bantuan Sosial PKH 2025', 
'Kepada warga yang masuk dalam Data Terpadu Kesejahteraan Sosial (DTKS), diberitahukan bahwa penyaluran bantuan Program Keluarga Harapan (PKH) Tahap 1 Tahun 2025 akan segera dilaksanakan.\n\nPenyaluran bantuan dijadwalkan pada tanggal 20-25 April 2025 melalui rekening bank atau kantor pos terdekat. Bagi penerima baru, harap membawa KTP, KK, dan surat keterangan dari RT/RW.\n\nUntuk informasi lebih lanjut, silakan hubungi petugas pendamping PKH di kantor desa.',
'2025-04-08'),

('Kerja Bakti Massal Membersihkan Lingkungan Desa', 
'Dalam rangka menjaga kebersihan dan keindahan desa, Pemerintah Desa Sukamaju mengajak seluruh warga untuk ikut berpartisipasi dalam kegiatan Kerja Bakti Massal yang akan dilaksanakan pada:\n\nHari/Tanggal: Minggu, 20 April 2025\nWaktu: Pukul 07.00 - 10.00 WIB\nLokasi: Seluruh wilayah Desa Sukamaju\n\nKegiatan ini meliputi pembersihan selokan, pengecatan tembok, penanaman pohon, dan pembersihan fasilitas umum. Mohon kehadiran seluruh warga demi terciptanya lingkungan desa yang bersih dan nyaman.',
'2025-04-05'),

('Pengumuman Perbaikan Sistem Air Bersih', 
'Diberitahukan kepada seluruh pelanggan sistem air bersih desa bahwa akan dilakukan perbaikan jaringan pipa distribusi utama pada:\n\nHari/Tanggal: Kamis-Jumat, 17-18 April 2025\nJam: 08.00 - 16.00 WIB\n\nSelama perbaikan berlangsung, distribusi air bersih akan terhenti sementara untuk wilayah Dusun Cikaret dan Dusun Mekarjaya. Mohon warga menyiapkan stok air sebelumnya.\n\nKami memohon maaf atas ketidaknyamanan ini. Perbaikan dilakukan untuk meningkatkan kualitas layanan air bersih bagi seluruh warga.',
'2025-04-03');

-- =============================================
-- Data Dummy: Pesan
-- =============================================
INSERT INTO pesan (nama, email, subjek, isi, tanggal, is_read) VALUES
('Budi Santoso', 'budi@email.com', 'Pertanyaan tentang IMB', 'Selamat pagi, saya ingin menanyakan prosedur pengurusan Izin Mendirikan Bangunan (IMB) di desa ini. Apa saja syarat dan berkasnya? Berapa lama prosesnya? Terima kasih atas perhatiannya.', '2025-04-10 09:30:00', 1),
('Siti Rahma', 'siti@email.com', 'Pengaduan Jalan Rusak', 'Yth. Bapak/Ibu Perangkat Desa, saya ingin melaporkan kondisi jalan di RT 03 RW 02 yang sudah rusak parah dan berlubang. Kondisi ini sangat membahayakan pengguna jalan, terutama di malam hari. Mohon segera ditindaklanjuti.', '2025-04-09 14:15:00', 1),
('Ahmad Fauzi', 'ahmad@email.com', 'Informasi Bantuan UMKM', 'Kepada yang berwenang, saya adalah pelaku UMKM di desa ini. Saya ingin mengetahui informasi mengenai program bantuan modal usaha untuk UMKM yang ada di desa. Apakah ada program seperti itu? Bagaimana cara mendaftarnya? Terima kasih.', '2025-04-07 11:00:00', 0),
('Dewi Purwanti', 'dewi@email.com', 'Keluhan Pelayanan Kesehatan', 'Saya ingin menyampaikan masukan mengenai pelayanan Posyandu. Jadwal Posyandu sering berubah tanpa pemberitahuan sebelumnya, sehingga warga kesulitan. Mohon ada pengumuman yang lebih jelas dan tepat waktu. Terima kasih.', '2025-04-06 16:45:00', 0);
