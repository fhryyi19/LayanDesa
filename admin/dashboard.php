<?php
/**
 * admin/dashboard.php - Halaman Dashboard Admin
 */

require_once 'auth.php';

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

require_once 'layout.php';

// Statistik
$totalBerita      = (int) $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$totalPengumuman  = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
$totalPesan       = (int) $pdo->query("SELECT COUNT(*) FROM pesan")->fetchColumn();
$unreadPesan      = (int) $pdo->query("SELECT COUNT(*) FROM pesan WHERE is_read = 0")->fetchColumn();
try {
    $totalKeluhan    = (int) $pdo->query("SELECT COUNT(*) FROM keluhan")->fetchColumn();
    $pendingKeluhan2 = (int) $pdo->query("SELECT COUNT(*) FROM keluhan WHERE status = 'menunggu'")->fetchColumn();
} catch (PDOException $e) {
    $totalKeluhan = 0; $pendingKeluhan2 = 0;
}

// Berita terbaru (5)
$recentBerita = $pdo->query("SELECT id, judul, tanggal FROM berita ORDER BY tanggal DESC LIMIT 5")->fetchAll();

// Pesan masuk terbaru (5)
$recentPesan = $pdo->query("SELECT id, nama, subjek, tanggal, is_read FROM pesan ORDER BY tanggal DESC LIMIT 5")->fetchAll();
?>

<!-- Info Selamat Datang (top of dashboard) -->
<div class="welcome-bar">
    <div class="welcome-bar-icon" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    </div>
    <div>
        <div class="welcome-bar-name">Selamat Datang, <?= htmlspecialchars($adminName) ?></div>
        <div class="welcome-bar-date">
            <span>Hari ini: <strong><?= date('l, d F Y') ?></strong></span>
            <span>|</span>
            <?php if ($unreadPesan > 0): ?>
            <span class="welcome-unread">Ada <?= $unreadPesan ?> pesan baru yang belum dibaca.</span>
            <?php else: ?>
            <span class="welcome-all-read">Semua pesan sudah dibaca.</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-card-icon green" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $totalBerita ?></span>
            <span class="stat-label">Total Berita</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon gold" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $totalPengumuman ?></span>
            <span class="stat-label">Pengumuman</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon blue" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $totalPesan ?></span>
            <span class="stat-label">Total Pesan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon red" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $unreadPesan ?></span>
            <span class="stat-label">Pesan Belum Dibaca</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon gold" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $totalKeluhan ?></span>
            <span class="stat-label">Total Keluhan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon red" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-card-info">
            <span class="stat-value"><?= $pendingKeluhan2 ?></span>
            <span class="stat-label">Keluhan Menunggu</span>
        </div>
    </div>

</div>

<!-- Tabel & Aktivitas -->
<div class="dashboard-grid">

    <!-- Berita Terbaru -->
    <div class="table-card">
        <div class="table-card-header">
            <span class="table-card-title">Berita Terbaru</span>
            <a href="berita.php" class="btn-admin btn-admin-secondary btn-admin-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentBerita)): ?>
                    <tr>
                        <td colspan="3" class="td-empty">Belum ada berita.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentBerita as $b): ?>
                    <tr>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?= htmlspecialchars($b['judul']) ?>
                        </td>
                        <td class="td-date">
                            <?= date('d/m/Y', strtotime($b['tanggal'])) ?>
                        </td>
                        <td>
                            <a href="berita-edit.php?id=<?= $b['id'] ?>" class="btn-admin btn-admin-warning btn-admin-sm">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-add-row">
            <a href="berita-tambah.php" class="btn-admin btn-admin-primary btn-admin-sm" id="btn-tambah-berita-dash">
                + Tambah Berita
            </a>
        </div>
    </div>

    <!-- Pesan Masuk Terbaru -->
    <div class="table-card">
        <div class="table-card-header">
            <span class="table-card-title">Pesan Masuk Terbaru</span>
            <a href="pesan.php" class="btn-admin btn-admin-secondary btn-admin-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pengirim</th>
                        <th>Subjek</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPesan)): ?>
                    <tr>
                        <td colspan="3" class="td-empty">Belum ada pesan.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentPesan as $p): ?>
                    <tr>
                        <td class="<?= $p['is_read'] ? '' : 'fw-bold' ?>">
                            <?= htmlspecialchars($p['nama']) ?>
                        </td>
                        <td style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" class="td-date">
                            <?= htmlspecialchars($p['subjek']) ?>
                        </td>
                        <td>
                            <?php if ($p['is_read']): ?>
                            <span class="status-badge status-badge--read">Dibaca</span>
                            <?php else: ?>
                            <span class="status-badge status-badge--new">Baru</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once 'layout_end.php'; ?>
