<?php
/**
 * layanan.php - Halaman Informasi Layanan Desa
 */

require_once 'config/database.php';

$pageTitle  = 'Informasi Layanan';
$activePage = 'layanan';
$basePath   = '';
$metaDesc   = 'Daftar layanan administrasi dan syarat pengurusan di Desa Sukamaju.';

include 'includes/header.php';

// SVG icons per layanan (inline, satu per layanan sesuai kategori)
$layananIcons = [
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>',
];

$layananList = [
    ['nama' => 'Surat Keterangan Tidak Mampu (SKTM)', 'desc' => 'Surat keterangan yang menyatakan bahwa seseorang tergolong kurang mampu secara ekonomi.', 'syarat' => ['Fotokopi KTP pemohon', 'Fotokopi Kartu Keluarga (KK)', 'Surat pengantar dari RT/RW', 'Pas foto 3x4 (2 lembar)'], 'waktu' => '1 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Surat Keterangan Domisili', 'desc' => 'Surat keterangan yang menyatakan bahwa seseorang berdomisili di wilayah Desa Sukamaju.', 'syarat' => ['Fotokopi KTP pemohon', 'Fotokopi Kartu Keluarga (KK)', 'Surat pengantar dari RT/RW', 'Surat keterangan pindah (jika baru pindah)'], 'waktu' => '1 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Pengurusan Kartu Keluarga (KK)', 'desc' => 'Pembuatan atau pembaruan Kartu Keluarga untuk warga desa Sukamaju.', 'syarat' => ['KTP asli kepala keluarga', 'Akta nikah / akta cerai', 'Surat keterangan pindah (jika pindah)', 'Akta kelahiran (untuk penambahan anggota keluarga)', 'Surat pengantar dari RT/RW'], 'waktu' => '3–7 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Surat Keterangan Usaha', 'desc' => 'Surat yang menerangkan bahwa seseorang menjalankan usaha tertentu di wilayah desa.', 'syarat' => ['Fotokopi KTP pemohon', 'Fotokopi Kartu Keluarga (KK)', 'Surat pengantar dari RT/RW', 'Foto lokasi usaha'], 'waktu' => '1–2 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Izin Mendirikan Bangunan (IMB)', 'desc' => 'Izin yang wajib dimiliki sebelum mendirikan atau merenovasi bangunan di wilayah desa.', 'syarat' => ['Fotokopi KTP pemohon', 'Sertifikat tanah atau bukti kepemilikan', 'Gambar/denah bangunan yang direncanakan', 'Surat pernyataan tidak sengketa', 'Surat pengantar dari RT/RW'], 'waktu' => '7–14 hari kerja', 'biaya' => 'Gratis (desa) / sesuai perda (kabupaten)'],
    ['nama' => 'Surat Keterangan Lahir', 'desc' => 'Surat keterangan kelahiran dari desa sebagai dasar pengurusan akta kelahiran.', 'syarat' => ['Surat keterangan lahir dari bidan/dokter/RS', 'Fotokopi KTP kedua orang tua', 'Fotokopi Kartu Keluarga (KK)', 'Akta nikah orang tua', 'Surat pengantar dari RT/RW'], 'waktu' => '1 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Surat Keterangan Ahli Waris', 'desc' => 'Surat yang menerangkan siapa saja yang berhak sebagai ahli waris dari seseorang yang meninggal dunia.', 'syarat' => ['Surat kematian yang bersangkutan', 'Fotokopi KTP para ahli waris', 'Fotokopi Kartu Keluarga (KK)', 'Surat pengantar dari RT/RW', 'Akta nikah almarhum/almarhumah'], 'waktu' => '2–3 hari kerja', 'biaya' => 'Gratis'],
    ['nama' => 'Surat Pengantar SKCK', 'desc' => 'Surat pengantar dari desa sebagai syarat pembuatan Surat Keterangan Catatan Kepolisian (SKCK).', 'syarat' => ['Fotokopi KTP pemohon', 'Fotokopi Kartu Keluarga (KK)', 'Surat pengantar dari RT/RW', 'Pas foto 4x6 (4 lembar)'], 'waktu' => '1 hari kerja', 'biaya' => 'Gratis'],
];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Layanan Administrasi Desa</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <span>Layanan</span>
            </nav>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">

        <!-- Info Banner -->
        <div class="alert alert-info mb-8">
            <div class="alert-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <strong>Jam Pelayanan:</strong> Senin – Jumat, pukul 08.00 – 16.00 WIB (kecuali hari libur nasional).
                Datang langsung ke Kantor Desa Sukamaju dengan membawa berkas yang diperlukan.
            </div>
        </div>

        <!-- Daftar Layanan -->
        <div class="grid grid-2 grid-gap-6">
            <?php foreach ($layananList as $idx => $layanan): ?>
            <div class="layanan-card" id="layanan-<?= $idx + 1 ?>">
                <div class="layanan-card-body">

                    <!-- Header Layanan -->
                    <div class="layanan-header">
                        <div class="layanan-icon-box" aria-hidden="true">
                            <?= $layananIcons[$idx] ?? $layananIcons[0] ?>
                        </div>
                        <div>
                            <h2 class="layanan-name"><?= htmlspecialchars($layanan['nama']) ?></h2>
                            <p class="layanan-desc"><?= htmlspecialchars($layanan['desc']) ?></p>
                        </div>
                    </div>

                    <!-- Info Cepat -->
                    <div class="layanan-meta">
                        <span class="layanan-meta-tag">
                            <strong>Estimasi:</strong> <?= $layanan['waktu'] ?>
                        </span>
                        <span class="layanan-meta-tag">
                            <strong>Biaya:</strong> <?= $layanan['biaya'] ?>
                        </span>
                    </div>

                    <!-- Persyaratan -->
                    <details class="layanan-details">
                        <summary>
                            Lihat Persyaratan
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        </summary>
                        <ul class="layanan-syarat">
                            <?php foreach ($layanan['syarat'] as $syarat): ?>
                            <li><?= htmlspecialchars($syarat) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </details>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div class="layanan-cta">
            <div class="layanan-cta-icon" aria-hidden="true">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h3>Ada Pertanyaan?</h3>
            <p>Hubungi kami atau datang langsung ke kantor desa untuk informasi lebih lanjut.</p>
            <a href="kontak.php" class="btn btn-green" id="btn-kontak-layanan">Kirim Pertanyaan</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
