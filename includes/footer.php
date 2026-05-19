<?php
/**
 * Footer - LayanDesa
 * Digunakan di semua halaman public
 */
?>

<!-- ============================
     FOOTER
     ============================ -->
<footer class="footer" role="contentinfo">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">

                <!-- Brand & Deskripsi -->
                <div class="footer-col footer-brand">
                    <div class="footer-logo">
                        <div class="brand-icon-sm" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                        </div>
                        <span class="brand-name">LayanDesa</span>
                    </div>
                    <p>Portal resmi pelayanan publik <strong style="color: rgba(255,255,255,0.8);">Desa Sukamaju</strong>, Kecamatan Cikaret, Kabupaten Sukabumi. Melayani masyarakat dengan transparan, cepat, dan profesional.</p>
                </div>

                <!-- Menu Navigasi -->
                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="<?= $basePath ?? '' ?>index.php">Beranda</a></li>
                        <li><a href="<?= $basePath ?? '' ?>profil.php">Profil Desa</a></li>
                        <li><a href="<?= $basePath ?? '' ?>berita.php">Berita</a></li>
                        <li><a href="<?= $basePath ?? '' ?>pengumuman.php">Pengumuman</a></li>
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">Layanan</a></li>
                        <li><a href="<?= $basePath ?? '' ?>kontak.php">Kontak & Pengaduan</a></li>
                    </ul>
                </div>

                <!-- Layanan -->
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">Surat Keterangan</a></li>
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">Kartu Keluarga</a></li>
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">Surat Domisili</a></li>
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">Izin Mendirikan Bangunan</a></li>
                        <li><a href="<?= $basePath ?? '' ?>layanan.php">SKCK & Lainnya</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="footer-col">
                    <h4>Kontak Desa</h4>
                    <div class="footer-contact-item">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <span>Jl. Desa Sukamaju No. 1, Cikaret, Sukabumi, Jawa Barat</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 10.23a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                        </span>
                        <span>(0266) 123-456</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <span>desa.sukamaju@gmail.com</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <span>Senin – Jumat: 08.00 – 16.00 WIB</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <hr class="footer-divider">

    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> LayanDesa – Desa Sukamaju. Hak cipta dilindungi.</p>
            <p>Sistem Pelayanan Publik Digital untuk <a href="<?= $basePath ?? '' ?>index.php">masyarakat desa</a></p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="<?= $basePath ?? '' ?>assets/js/main.js" defer></script>
</body>
</html>
