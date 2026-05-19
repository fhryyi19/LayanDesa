<?php
/**
 * index.php - Halaman Utama LayanDesa
 * Menampilkan: Hero, Statistik, Berita Terbaru, Pengumuman, Layanan, CTA
 */

require_once 'config/database.php';

$pageTitle  = 'Beranda';
$activePage = 'home';
$basePath   = '';

// --- Ambil 3 berita terbaru ---
$stmtBerita = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal FROM berita ORDER BY tanggal DESC LIMIT 3");
$stmtBerita->execute();
$beritaList = $stmtBerita->fetchAll();

// --- Ambil 4 pengumuman terbaru ---
$stmtPengumuman = $pdo->prepare("SELECT id, judul, isi, tanggal FROM pengumuman ORDER BY tanggal DESC LIMIT 4");
$stmtPengumuman->execute();
$pengumumanList = $stmtPengumuman->fetchAll();

// --- Hitung total data ---
$totalBerita     = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$totalPengumuman = $pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
$totalPesan      = $pdo->query("SELECT COUNT(*) FROM pesan")->fetchColumn();

include 'includes/header.php';
?>

<!-- ============================
     HERO SECTION
     ============================ -->
<section class="hero" id="beranda" aria-label="Beranda Desa Sukamaju">
    <div class="hero-bg-pattern" aria-hidden="true"></div>
    <div class="hero-shapes" aria-hidden="true"></div>

    <div class="hero-content">
        <!-- Text Side -->
        <div class="hero-text">
            <div class="hero-badge" aria-label="Status desa aktif">
                <span class="hero-badge-dot"></span>
                Desa Digital Aktif 2025
            </div>

            <h1>
                Selamat Datang di<br>
                <span class="highlight">LayanDesa</span><br>
                Sukamaju
            </h1>

            <p>
                Portal resmi pelayanan publik Desa Sukamaju. Akses informasi desa, berita terkini,
                pengumuman penting, dan layanan administrasi kapan saja, di mana saja.
            </p>

            <div class="hero-actions">
                <a href="layanan.php" class="btn btn-primary" id="hero-cta-layanan">
                    Lihat Layanan
                </a>
                <a href="kontak.php" class="btn btn-outline-white" id="hero-cta-kontak">
                    Hubungi Kami
                </a>
            </div>

            <div class="hero-stats" aria-label="Statistik Desa">
                <div class="hero-stat">
                    <span class="number" aria-label="Jumlah penduduk">3.247</span>
                    <span class="label">Penduduk</span>
                </div>
                <div class="hero-stat">
                    <span class="number" aria-label="Jumlah RT">24</span>
                    <span class="label">RT</span>
                </div>
                <div class="hero-stat">
                    <span class="number" aria-label="Jumlah RW">6</span>
                    <span class="label">RW</span>
                </div>
                <div class="hero-stat">
                    <span class="number" aria-label="Jumlah dusun">3</span>
                    <span class="label">Dusun</span>
                </div>
            </div>
        </div>

        <!-- Visual Side -->
        <div class="hero-visual" aria-hidden="true">
            <div class="hero-card-stack">
                <div class="hero-main-card">
                    <div class="hero-card-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                    </div>
                    <h3>Pelayanan Publik Digital</h3>
                    <p>Urus administrasi desa lebih mudah, cepat, dan transparan melalui portal digital kami.</p>
                </div>

                <div class="floating-card card-1">
                    <div class="fc-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="fc-text">
                        <div class="fc-label">Pengumuman</div>
                        <div class="fc-value"><?= $totalPengumuman ?> Aktif</div>
                    </div>
                </div>

                <div class="floating-card card-2">
                    <div class="fc-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                    </div>
                    <div class="fc-text">
                        <div class="fc-label">Berita Desa</div>
                        <div class="fc-value"><?= $totalBerita ?> Artikel</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     STATISTIK
     ============================ -->
<section class="stats-section" aria-label="Statistik Desa">
    <div class="container">
        <div class="grid grid-4 grid-gap-0">
            <div class="stat-item">
                <span class="stat-number">3.247</span>
                <span class="stat-label">Jumlah Penduduk</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">847</span>
                <span class="stat-label">Jumlah KK</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">24</span>
                <span class="stat-label">Jumlah RT</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $totalBerita ?>+</span>
                <span class="stat-label">Berita Dipublikasikan</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     LAYANAN UNGGULAN
     ============================ -->
<section class="section" id="layanan" aria-labelledby="layanan-title">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Layanan Kami</div>
            <h2 class="section-title" id="layanan-title">Layanan Administrasi Desa</h2>
            <p class="section-subtitle">
                Berbagai layanan administrasi kependudukan yang tersedia di Desa Sukamaju untuk memenuhi kebutuhan masyarakat.
            </p>
        </div>

        <div class="grid grid-3 grid-gap-6">

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <h3>Surat Keterangan</h3>
                <p>Pembuatan berbagai surat keterangan seperti SKCK, keterangan tidak mampu, keterangan domisili, dan lainnya.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
                </div>
                <h3>Kartu Keluarga</h3>
                <p>Pengurusan pembuatan atau pembaruan Kartu Keluarga (KK) untuk warga desa Sukamaju.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                </div>
                <h3>Surat Domisili</h3>
                <p>Penerbitan surat keterangan domisili untuk keperluan pendidikan, pekerjaan, maupun administrasi lainnya.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <h3>Izin Mendirikan Bangunan</h3>
                <p>Pengurusan Izin Mendirikan Bangunan (IMB) untuk konstruksi baru maupun renovasi properti.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <h3>Surat Usaha</h3>
                <p>Pembuatan surat keterangan usaha untuk UMKM dan pelaku usaha di wilayah Desa Sukamaju.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <h3>Surat Keterangan Sehat</h3>
                <p>Penerbitan surat keterangan sehat melalui kerja sama dengan Puskesmas dan tenaga kesehatan desa.</p>
            </div>

        </div>

        <div class="text-center mt-10">
            <a href="layanan.php" class="btn btn-green" id="btn-lihat-semua-layanan">
                Lihat Semua Layanan &amp; Syarat
            </a>
        </div>
    </div>
</section>

<!-- ============================
     BERITA TERBARU
     ============================ -->
<section class="section section-alt" id="berita" aria-labelledby="berita-title">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Informasi Terkini</div>
            <h2 class="section-title" id="berita-title">Berita Desa Terbaru</h2>
            <p class="section-subtitle">
                Ikuti perkembangan terbaru kegiatan dan pembangunan di Desa Sukamaju.
            </p>
        </div>

        <?php if (empty($beritaList)): ?>
        <div class="text-center text-muted section-empty">
            <div class="empty-icon-wrap" aria-hidden="true">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
            </div>
            <p>Belum ada berita yang dipublikasikan.</p>
        </div>
        <?php else: ?>

        <div class="grid grid-3 grid-gap-6">
            <?php foreach ($beritaList as $berita): ?>
            <article class="news-card">
                <?php if (!empty($berita['gambar']) && file_exists('uploads/berita/' . $berita['gambar'])): ?>
                <div class="news-card-img" style="background-image: url('uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>'); background-size: cover; background-position: center;" aria-label="Gambar Berita">
                </div>
                <?php else: ?>
                <div class="news-card-img news-card-img--placeholder" aria-hidden="true">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                </div>
                <?php endif; ?>
                <div class="news-card-body">
                    <div class="news-card-date">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= formatTanggal($berita['tanggal']) ?>
                    </div>
                    <h3 class="news-card-title">
                        <?= htmlspecialchars($berita['judul']) ?>
                    </h3>
                    <p class="news-card-excerpt">
                        <?= htmlspecialchars(truncateText($berita['isi'], 130)) ?>
                    </p>
                </div>
                <div class="news-card-footer">
                    <a href="berita-detail.php?id=<?= $berita['id'] ?>" class="btn btn-outline-green btn-sm" id="btn-baca-berita-<?= $berita['id'] ?>">
                        Baca Selengkapnya
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="berita.php" class="btn btn-green" id="btn-semua-berita">Lihat Semua Berita</a>
        </div>

        <?php endif; ?>
    </div>
</section>

<!-- ============================
     PENGUMUMAN
     ============================ -->
<section class="section" id="pengumuman" aria-labelledby="pengumuman-title">
    <div class="container">
        <div class="grid grid-2 kontak-layout">

            <!-- Pengumuman List -->
            <div>
                <div class="section-badge mb-4">Pengumuman</div>
                <h2 class="section-title" id="pengumuman-title">Pengumuman Terbaru</h2>
                <p class="section-subtitle mb-8">
                    Informasi penting dan pengumuman resmi dari pemerintah Desa Sukamaju.
                </p>

                <?php if (empty($pengumumanList)): ?>
                <p class="text-muted">Belum ada pengumuman.</p>
                <?php else: ?>
                <?php foreach ($pengumumanList as $pgm): ?>
                <article class="announcement-item">
                    <div class="ann-date">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= formatTanggal($pgm['tanggal']) ?>
                    </div>
                    <div class="ann-title"><?= htmlspecialchars($pgm['judul']) ?></div>
                    <div class="ann-excerpt"><?= htmlspecialchars(truncateText($pgm['isi'], 100)) ?></div>
                </article>
                <?php endforeach; ?>
                <a href="pengumuman.php" class="btn btn-outline-green btn-sm pgm-more-link" id="btn-semua-pengumuman">
                    Lihat Semua Pengumuman
                </a>
                <?php endif; ?>
            </div>

            <!-- Info Kontak -->
            <div>
                <div class="card index-contact-card">
                    <div class="card-body">
                        <div class="contact-card-icon-wrap" aria-hidden="true">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 10.23a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                        </div>
                        <h3 class="index-contact-title">Hubungi Kantor Desa</h3>
                        <div class="index-contact-list">
                            <div class="index-contact-row">
                                <span class="contact-info-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <div>
                                    <div class="index-contact-item-label">Alamat</div>
                                    <div class="index-contact-item-value">Jl. Desa Sukamaju No. 1, Cikaret, Sukabumi</div>
                                </div>
                            </div>
                            <div class="index-contact-row">
                                <span class="contact-info-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 10.23a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                                </span>
                                <div>
                                    <div class="index-contact-item-label">Telepon</div>
                                    <div class="index-contact-item-value">(0266) 123-456</div>
                                </div>
                            </div>
                            <div class="index-contact-row">
                                <span class="contact-info-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <div>
                                    <div class="index-contact-item-label">Email</div>
                                    <div class="index-contact-item-value">desa.sukamaju@gmail.com</div>
                                </div>
                            </div>
                            <div class="index-contact-row">
                                <span class="contact-info-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <div>
                                    <div class="index-contact-item-label">Jam Pelayanan</div>
                                    <div class="index-contact-item-value">Senin – Jumat: 08.00 – 16.00 WIB</div>
                                </div>
                            </div>
                        </div>
                        <div class="index-contact-cta-wrap">
                            <a href="kontak.php" class="btn btn-green w-100 btn-center" id="btn-kontak-main">
                                Kirim Pesan / Pengaduan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     CTA SECTION
     ============================ -->
<section class="cta-section">
    <div class="cta-bg" aria-hidden="true"></div>
    <div class="container text-center cta-content">
        <div class="cta-icon-wrap" aria-hidden="true">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
        </div>
        <h2>Butuh Bantuan Pelayanan?</h2>
        <p>
            Tim kami siap membantu Anda dalam mengurus berbagai administrasi kependudukan dengan cepat dan mudah.
        </p>
        <div class="cta-actions">
            <a href="layanan.php" class="btn btn-primary" id="cta-layanan">Lihat Layanan</a>
            <a href="kontak.php" class="btn btn-outline-white" id="cta-kontak">Kirim Pengaduan</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
