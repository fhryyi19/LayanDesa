<?php
/**
 * berita-detail.php - Detail Berita
 */

require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: berita.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal FROM berita WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$berita = $stmt->fetch();

if (!$berita) {
    $pageTitle  = 'Berita Tidak Ditemukan';
    $activePage = 'berita';
    $basePath   = '';
    include 'includes/header.php';
    echo '<div class="container text-center not-found-wrap"><h2>Berita tidak ditemukan.</h2><a href="berita.php" class="btn btn-green mt-6">&larr; Kembali</a></div>';
    include 'includes/footer.php';
    exit;
}

$pageTitle  = htmlspecialchars($berita['judul']);
$activePage = 'berita';
$basePath   = '';
$metaDesc   = truncateText($berita['isi'], 160);

$stmtRelated = $pdo->prepare("SELECT id, judul, tanggal FROM berita WHERE id != :id ORDER BY tanggal DESC LIMIT 3");
$stmtRelated->execute([':id' => $id]);
$related = $stmtRelated->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-header-title-detail"><?= htmlspecialchars($berita['judul']) ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <a href="berita.php">Berita</a>
                <span class="sep">›</span>
                <span>Detail</span>
            </nav>
        </div>
    </div>
</div>

<!-- Detail Berita -->
<section class="content-section">
    <div class="container">
        <div class="detail-layout">

            <!-- Artikel Utama -->
            <article>
                <div class="card">
                    <div class="card-body">
                        <!-- Gambar Berita -->
                        <?php if (!empty($berita['gambar']) && file_exists('uploads/berita/' . $berita['gambar'])): ?>
                        <div class="article-img-wrap">
                            <img src="uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>"
                                 alt="<?= htmlspecialchars($berita['judul']) ?>"
                                 class="article-img">
                        </div>
                        <?php endif; ?>

                        <!-- Meta -->
                        <div class="article-meta">
                            <span class="badge badge-primary">Berita</span>
                            <span class="article-meta-date">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?= formatTanggal($berita['tanggal']) ?>
                            </span>
                        </div>

                        <!-- Isi Konten -->
                        <div class="article-content">
                            <?= htmlspecialchars($berita['isi']) ?>
                        </div>

                        <!-- Footer Artikel -->
                        <div class="article-footer">
                            <a href="berita.php" class="btn btn-outline-green btn-sm" id="btn-back-berita">&larr; Kembali ke Berita</a>
                            <span class="article-footer-pub">
                                Dipublikasikan: <?= formatTanggal($berita['tanggal']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Sidebar -->
            <aside>
                <!-- Berita Terkait -->
                <?php if (!empty($related)): ?>
                <div class="card mb-6">
                    <div class="card-body">
                        <h3 class="sidebar-card-title">Berita Lainnya</h3>
                        <div class="related-list">
                            <?php foreach ($related as $r): ?>
                            <a href="berita-detail.php?id=<?= $r['id'] ?>"
                               class="related-link"
                               id="btn-related-<?= $r['id'] ?>">
                                <div class="related-link-date">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <?= formatTanggal($r['tanggal']) ?>
                                </div>
                                <div class="related-link-title">
                                    <?= htmlspecialchars($r['judul']) ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info Desa -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="sidebar-card-title">Informasi Desa</h3>
                        <p class="sidebar-info-text">
                            Untuk informasi lebih lanjut, hubungi kantor Desa Sukamaju.
                        </p>
                        <a href="kontak.php" class="btn btn-green btn-sm w-100 btn-center" id="btn-kontak-detail">
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
