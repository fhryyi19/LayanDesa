<?php
/**
 * pengumuman.php - Halaman Pengumuman Desa
 */

require_once 'config/database.php';

$pageTitle  = 'Pengumuman';
$activePage = 'pengumuman';
$basePath   = '';
$metaDesc   = 'Pengumuman resmi dari Pemerintah Desa Sukamaju.';

$stmt = $pdo->query("SELECT id, judul, isi, tanggal FROM pengumuman ORDER BY tanggal DESC");
$pengumumanList = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Pengumuman Desa Sukamaju</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <span>Pengumuman</span>
            </nav>
        </div>
    </div>
</div>

<!-- Daftar Pengumuman -->
<section class="content-section">
    <div class="container">

        <?php if (empty($pengumumanList)): ?>
        <div class="text-center section-empty-lg">
            <div class="empty-icon-wrap" aria-hidden="true">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h3 class="text-muted">Belum Ada Pengumuman</h3>
            <p class="text-muted mt-2">Pengumuman akan tampil di sini setelah diterbitkan.</p>
        </div>
        <?php else: ?>

        <div class="pgm-list-wrap">
            <?php foreach ($pengumumanList as $index => $pgm): ?>
            <article class="card mb-6" id="pgm-<?= $pgm['id'] ?>">
                <div class="card-body">
                    <div class="pgm-card-header">
                        <div class="pgm-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </div>
                        <div>
                            <div class="pgm-title"><?= htmlspecialchars($pgm['judul']) ?></div>
                            <div class="pgm-date">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?= formatTanggal($pgm['tanggal']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="pgm-content" id="isi-pgm-<?= $pgm['id'] ?>"><?= htmlspecialchars($pgm['isi']) ?></div>
                    <button class="pgm-toggle-btn" onclick="togglePgm(<?= $pgm['id'] ?>, this)" id="btn-toggle-pgm-<?= $pgm['id'] ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        Baca Selengkapnya
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<script>
function togglePgm(id, btn) {
    const content = document.getElementById('isi-pgm-' + id);
    const isExpanded = content.style.maxHeight && content.style.maxHeight !== '100px';
    const chevronDown = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
    const chevronUp = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>';
    if (isExpanded) {
        content.style.maxHeight = '100px';
        btn.innerHTML = chevronDown + ' Baca Selengkapnya';
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        btn.innerHTML = chevronUp + ' Sembunyikan';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
