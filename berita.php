<?php
/**
 * berita.php - Daftar Berita Desa
 */

require_once 'config/database.php';

$pageTitle  = 'Berita Desa';
$activePage = 'berita';
$basePath   = '';
$metaDesc   = 'Berita terkini dan informasi kegiatan Desa Sukamaju.';

$perPage    = 6;
$page       = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset     = ($page - 1) * $perPage;

$total      = (int)$pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal FROM berita ORDER BY tanggal DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$beritaList = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Berita Desa Sukamaju</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <span>Berita</span>
            </nav>
        </div>
    </div>
</div>

<!-- Daftar Berita -->
<section class="content-section">
    <div class="container">

        <?php if (empty($beritaList)): ?>
        <div class="text-center section-empty-lg">
            <div class="empty-icon-wrap" aria-hidden="true">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
            </div>
            <h3 class="text-muted">Belum Ada Berita</h3>
            <p class="text-muted mt-2">Berita akan tampil di sini setelah dipublikasikan oleh admin.</p>
        </div>
        <?php else: ?>

        <div class="grid grid-3 news-grid">
            <?php foreach ($beritaList as $b): ?>
            <article class="news-card">
                <?php if (!empty($b['gambar']) && file_exists('uploads/berita/' . $b['gambar'])): ?>
                <div class="news-card-img" style="background-image: url('uploads/berita/<?= htmlspecialchars($b['gambar']) ?>'); background-size: cover; background-position: center;" aria-label="Gambar Berita"></div>
                <?php else: ?>
                <div class="news-card-img news-card-img--placeholder" aria-hidden="true">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                </div>
                <?php endif; ?>
                <div class="news-card-body">
                    <div class="news-card-date">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= formatTanggal($b['tanggal']) ?>
                    </div>
                    <h2 class="news-card-title">
                        <?= htmlspecialchars($b['judul']) ?>
                    </h2>
                    <p class="news-card-excerpt">
                        <?= htmlspecialchars(truncateText($b['isi'], 130)) ?>
                    </p>
                </div>
                <div class="news-card-footer">
                    <a href="berita-detail.php?id=<?= $b['id'] ?>"
                       class="btn btn-outline-green btn-sm"
                       id="btn-berita-<?= $b['id'] ?>">
                        Baca Selengkapnya
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Navigasi halaman berita">
            <?php if ($page > 1): ?>
            <div class="page-item">
                <a href="?page=<?= $page - 1 ?>" class="page-link" id="btn-prev-page">&lsaquo; Sebelumnya</a>
            </div>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <div class="page-item">
                <a href="?page=<?= $i ?>"
                   class="page-link <?= ($i === $page) ? 'active' : '' ?>"
                   id="btn-page-<?= $i ?>">
                    <?= $i ?>
                </a>
            </div>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <div class="page-item">
                <a href="?page=<?= $page + 1 ?>" class="page-link" id="btn-next-page">Selanjutnya &rsaquo;</a>
            </div>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
