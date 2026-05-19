<?php
/**
 * profil.php - Halaman Profil Desa
 */

require_once 'config/database.php';

$pageTitle  = 'Profil Desa';
$activePage = 'profil';
$basePath   = '';
$metaDesc   = 'Profil Desa Sukamaju: sejarah, visi misi, struktur organisasi, dan data demografi desa.';

include 'includes/header.php';

$data = [
    ['Nama Desa', 'Sukamaju'], ['Kecamatan', 'Cikaret'], ['Kabupaten', 'Sukabumi'],
    ['Provinsi', 'Jawa Barat'], ['Kode Pos', '43155'], ['Luas Wilayah', '1.250 Ha'],
    ['Jumlah Dusun', '3 Dusun'], ['Jumlah RW', '6 RW'], ['Jumlah RT', '24 RT'],
    ['Jumlah Penduduk', '3.247 Jiwa'], ['Jumlah KK', '847 KK'],
    ['Kepala Desa', 'H. Asep Supriatna, S.Pd.'],
];

$misi = [
    'Meningkatkan kualitas pelayanan publik yang prima dan berorientasi pada kepuasan masyarakat.',
    'Mengembangkan infrastruktur desa yang memadai dan merata di seluruh wilayah.',
    'Memberdayakan ekonomi masyarakat melalui pengembangan UMKM dan pertanian modern.',
    'Meningkatkan kualitas pendidikan dan kesehatan masyarakat desa.',
    'Melestarikan budaya dan tradisi lokal sebagai identitas desa.',
    'Mewujudkan tata kelola pemerintahan desa yang transparan dan akuntabel.',
];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Profil Desa Sukamaju</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <span>Profil Desa</span>
            </nav>
        </div>
    </div>
</div>

<!-- Sejarah Singkat -->
<section class="content-section">
    <div class="container">
        <div class="grid grid-2 profil-layout-lg">
            <div>
                <div class="section-badge mb-4">Sejarah</div>
                <h2 class="section-title">Sekilas tentang<br>Desa Sukamaju</h2>
                <div class="article-content">
                    <p>Desa Sukamaju adalah sebuah desa yang terletak di Kecamatan Cikaret, Kabupaten Sukabumi, Provinsi Jawa Barat. Desa ini berdiri sejak tahun 1945 dan merupakan salah satu desa tertua di kecamatan yang memiliki kekayaan budaya dan alam yang melimpah.</p>
                    <p>Dengan luas wilayah sekitar 1.250 hektar, Desa Sukamaju terdiri dari 3 dusun, 6 RW, dan 24 RT. Mayoritas penduduk bermata pencaharian sebagai petani, pedagang, dan pegawai swasta.</p>
                    <p>Desa Sukamaju berkomitmen untuk terus berkembang dan memberikan pelayanan terbaik bagi masyarakatnya melalui program-program pembangunan yang inovatif dan partisipatif.</p>
                </div>
            </div>
            <div>
                <div class="card data-table-card">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4 profil-section-title">Data Singkat Desa</h3>
                        <table class="data-table">
                            <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= $row[0] ?></td>
                                <td><?= $row[1] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi -->
<section class="section section-alt" id="visi-misi" aria-labelledby="visi-misi-title">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Arah Pembangunan</div>
            <h2 class="section-title" id="visi-misi-title">Visi &amp; Misi Desa Sukamaju</h2>
        </div>

        <div class="grid grid-2 profil-layout-md">

            <!-- Visi -->
            <div class="visi-card">
                <div class="visi-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <div class="visi-label">Visi Desa</div>
                <blockquote class="visi-quote">
                    "Terwujudnya Desa Sukamaju yang Maju, Mandiri, Sejahtera, dan Berbudaya Berbasis Pertanian dan Pariwisata yang Berkelanjutan."
                </blockquote>
            </div>

            <!-- Misi -->
            <div class="card">
                <div class="card-body">
                    <div class="misi-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div class="misi-label">Misi Desa</div>
                    <ol class="misi-list">
                        <?php foreach ($misi as $m): ?>
                        <li><?= $m ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Struktur Organisasi -->
<section class="section" id="struktur" aria-labelledby="struktur-title">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Perangkat Desa</div>
            <h2 class="section-title" id="struktur-title">Struktur Organisasi</h2>
            <p class="section-subtitle">Pemerintah Desa Sukamaju Periode 2020–2026</p>
        </div>

        <!-- Org Chart -->
        <div class="org-chart">

            <!-- Kepala Desa -->
            <div class="org-node">
                <div class="org-card head">
                    <div class="org-avatar org-avatar-photo">
                        <img src="assets/img/perangkat/kepala-desa.png" alt="Foto H. Asep Supriatna, S.Pd. - Kepala Desa" loading="lazy">
                    </div>
                    <div class="org-name">H. Asep Supriatna, S.Pd.</div>
                    <div class="org-pos">Kepala Desa</div>
                </div>
            </div>
            <div class="org-connector"></div>

            <!-- Sekretaris -->
            <div class="org-node">
                <div class="org-card">
                    <div class="org-avatar org-avatar-photo">
                        <img src="assets/img/perangkat/sekretaris.png" alt="Foto Ibu Sari Dewi, S.Kom. - Sekretaris Desa" loading="lazy">
                    </div>
                    <div class="org-name">Ibu Sari Dewi, S.Kom.</div>
                    <div class="org-pos">Sekretaris Desa</div>
                </div>
            </div>
            <div class="org-connector"></div>

            <!-- Kaur Row -->
            <div class="org-row">
                <?php
                $kaur = [
                    ['Bpk. Andi Saputra',  'Kaur Pemerintahan', 'kaur-pemerintahan'],
                    ['Ibu Rini Lestari',   'Kaur Keuangan',     'kaur-keuangan'],
                    ['Bpk. Dedi Kurniawan','Kaur Umum & TU',    'kaur-umum'],
                    ['Bpk. Hendra Wijaya', 'Kaur Pembangunan',  'kaur-pembangunan'],
                ];
                foreach ($kaur as $k): ?>
                <div class="org-node">
                    <div class="org-card">
                        <div class="org-avatar org-avatar-photo">
                            <img src="assets/img/perangkat/<?= $k[2] ?>.png" alt="Foto <?= htmlspecialchars($k[0]) ?> - <?= htmlspecialchars($k[1]) ?>" loading="lazy">
                        </div>
                        <div class="org-name"><?= $k[0] ?></div>
                        <div class="org-pos"><?= $k[1] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
