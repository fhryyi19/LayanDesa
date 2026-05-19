<?php
/**
 * keluhan-saya.php - Halaman Tracking Keluhan Masyarakat
 */

require_once 'config/database.php';
require_once 'auth-masyarakat.php';

// Harus login
if (!isUserLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'keluhan-saya.php';
    header('Location: login-masyarakat.php');
    exit;
}

$pageTitle  = 'Keluhan Saya';
$activePage = 'kontak';
$basePath   = '';
$metaDesc   = 'Pantau status dan progres keluhan Anda di Desa Sukamaju.';

$user = getCurrentUser();

// ─── Detail Keluhan Tertentu ──────────────────────────────────────────
$detail   = null;
$timeline = [];
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT k.*, u.nama_lengkap FROM keluhan k JOIN users u ON k.user_id = u.id WHERE k.id = ? AND k.user_id = ?");
    $stmt->execute([$id, $user['id']]);
    $detail = $stmt->fetch();

    if ($detail) {
        $tStmt = $pdo->prepare("SELECT t.*, a.username as admin_nama FROM tanggapan_keluhan t LEFT JOIN admin a ON t.admin_id = a.id WHERE t.keluhan_id = ? ORDER BY t.created_at ASC");
        $tStmt->execute([$id]);
        $timeline = $tStmt->fetchAll();
    }
}

// ─── Daftar Semua Keluhan User ─────────────────────────────────────────
$filter = $_GET['status'] ?? '';
$allowedFilter = ['menunggu','diterima','diproses','selesai','ditolak',''];

if (!in_array($filter, $allowedFilter)) $filter = '';

$sql = "SELECT id, kode_tiket, judul, kategori, status, created_at FROM keluhan WHERE user_id = ?";
$params = [$user['id']];
if ($filter !== '') { $sql .= " AND status = ?"; $params[] = $filter; }
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$keluhanList = $stmt->fetchAll();

// Stats
$statsStmt = $pdo->prepare("SELECT status, COUNT(*) as cnt FROM keluhan WHERE user_id = ? GROUP BY status");
$statsStmt->execute([$user['id']]);
$statsRaw = $statsStmt->fetchAll();
$stats = ['menunggu'=>0,'diterima'=>0,'diproses'=>0,'selesai'=>0,'ditolak'=>0,'total'=>0];
foreach ($statsRaw as $s) {
    $stats[$s['status']] = (int)$s['cnt'];
    $stats['total'] += (int)$s['cnt'];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Keluhan Saya</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <a href="kontak.php">Kontak</a>
                <span class="sep">›</span>
                <span>Keluhan Saya</span>
            </nav>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">

        <!-- User Welcome Bar -->
        <div class="user-top-bar">
            <div class="user-top-info">
                <div class="user-top-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="user-top-name"><?= htmlspecialchars($user['nama']) ?></div>
                    <div class="user-top-email"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <div class="user-top-actions">
                <a href="kontak.php" class="btn btn-green btn-sm">+ Buat Keluhan Baru</a>
                <a href="logout-masyarakat.php" class="btn-link-logout">Keluar</a>
            </div>
        </div>

        <?php if ($detail): ?>
        <!-- ─── DETAIL VIEW ─── -->
        <div class="detail-back">
            <a href="keluhan-saya.php" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-2 detail-layout">

            <!-- Kiri: Info Keluhan -->
            <div>
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div>
                            <div class="detail-kode"><?= htmlspecialchars($detail['kode_tiket']) ?></div>
                            <h2 class="detail-judul"><?= htmlspecialchars($detail['judul']) ?></h2>
                        </div>
                        <span class="status-badge status-badge--<?= $detail['status'] ?> status-badge--lg">
                            <?= statusIcon($detail['status']) ?>
                            <?= statusLabel($detail['status']) ?>
                        </span>
                    </div>

                    <div class="detail-meta-grid">
                        <div class="detail-meta-item">
                            <span class="detail-meta-label">Kategori</span>
                            <span class="detail-meta-value"><?= htmlspecialchars($detail['kategori']) ?></span>
                        </div>
                        <div class="detail-meta-item">
                            <span class="detail-meta-label">Tanggal Lapor</span>
                            <span class="detail-meta-value"><?= formatTanggalWaktu($detail['created_at']) ?></span>
                        </div>
                        <?php if ($detail['lokasi']): ?>
                        <div class="detail-meta-item">
                            <span class="detail-meta-label">Lokasi</span>
                            <span class="detail-meta-value"><?= htmlspecialchars($detail['lokasi']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($detail['tanggal_kejadian']): ?>
                        <div class="detail-meta-item">
                            <span class="detail-meta-label">Tanggal Kejadian</span>
                            <span class="detail-meta-value"><?= formatTanggal($detail['tanggal_kejadian']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="detail-isi">
                        <div class="detail-isi-label">Isi Laporan</div>
                        <div class="detail-isi-text"><?= nl2br(htmlspecialchars($detail['isi'])) ?></div>
                    </div>

                    <?php if ($detail['foto_bukti']): ?>
                    <div class="detail-foto">
                        <div class="detail-isi-label">Foto Bukti</div>
                        <a href="<?= htmlspecialchars($detail['foto_bukti']) ?>" target="_blank" rel="noopener">
                            <img src="<?= htmlspecialchars($detail['foto_bukti']) ?>" alt="Foto bukti keluhan" class="detail-foto-img">
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($detail['status'] === 'ditolak' && $detail['alasan_penolakan']): ?>
                    <div class="alert alert-danger mt-4">
                        <div class="alert-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <div><strong>Alasan Penolakan:</strong> <?= nl2br(htmlspecialchars($detail['alasan_penolakan'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kanan: Timeline -->
            <div>
                <div class="timeline-card">
                    <h3 class="timeline-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Timeline Proses
                    </h3>
                    <div class="timeline">
                        <?php if (empty($timeline)): ?>
                        <div class="timeline-empty">Belum ada update</div>
                        <?php else: ?>
                        <?php foreach ($timeline as $t): ?>
                        <div class="timeline-item timeline-item--<?= $t['jenis'] ?>">
                            <div class="timeline-dot">
                                <?php if ($t['jenis'] === 'sistem'): ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                                <?php elseif ($t['jenis'] === 'status_update'): ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <?php else: ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-content">
                                <?php if ($t['status_baru']): ?>
                                <span class="timeline-status-badge status-badge--<?= $t['status_baru'] ?>"><?= statusLabel($t['status_baru']) ?></span>
                                <?php endif; ?>
                                <div class="timeline-pesan"><?= nl2br(htmlspecialchars($t['pesan'])) ?></div>
                                <div class="timeline-meta">
                                    <?php if ($t['admin_nama']): ?><span>Petugas: <?= htmlspecialchars($t['admin_nama']) ?></span><?php endif; ?>
                                    <span><?= formatTanggalWaktu($t['created_at']) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ─── LIST VIEW ─── -->

        <!-- Stats Row -->
        <div class="my-stats-grid">
            <div class="my-stat-item my-stat--total">
                <span class="my-stat-num"><?= $stats['total'] ?></span>
                <span class="my-stat-lbl">Total</span>
            </div>
            <div class="my-stat-item my-stat--menunggu">
                <span class="my-stat-num"><?= $stats['menunggu'] ?></span>
                <span class="my-stat-lbl">Menunggu</span>
            </div>
            <div class="my-stat-item my-stat--diproses">
                <span class="my-stat-num"><?= $stats['diproses'] ?></span>
                <span class="my-stat-lbl">Diproses</span>
            </div>
            <div class="my-stat-item my-stat--selesai">
                <span class="my-stat-num"><?= $stats['selesai'] ?></span>
                <span class="my-stat-lbl">Selesai</span>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <?php
            $tabs = ['' => 'Semua', 'menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
            foreach ($tabs as $val => $lbl): ?>
            <a href="keluhan-saya.php?status=<?= $val ?>" class="filter-tab <?= $filter === $val ? 'active' : '' ?>"><?= $lbl ?></a>
            <?php endforeach; ?>
        </div>

        <!-- List Keluhan -->
        <?php if (empty($keluhanList)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
            </div>
            <h3>Belum Ada Keluhan</h3>
            <p><?= $filter ? 'Tidak ada keluhan dengan status ini.' : 'Anda belum pernah mengirim keluhan.' ?></p>
            <a href="kontak.php" class="btn btn-green">Kirim Keluhan Pertama</a>
        </div>
        <?php else: ?>
        <div class="keluhan-list">
            <?php foreach ($keluhanList as $k): ?>
            <a href="keluhan-saya.php?id=<?= $k['id'] ?>" class="keluhan-list-item">
                <div class="keluhan-list-left">
                    <div class="keluhan-list-header">
                        <span class="keluhan-list-kode"><?= htmlspecialchars($k['kode_tiket']) ?></span>
                        <span class="keluhan-list-kat"><?= htmlspecialchars($k['kategori']) ?></span>
                    </div>
                    <div class="keluhan-list-judul"><?= htmlspecialchars($k['judul']) ?></div>
                    <div class="keluhan-list-date"><?= formatTanggalWaktu($k['created_at']) ?></div>
                </div>
                <div class="keluhan-list-right">
                    <span class="status-badge status-badge--<?= $k['status'] ?>">
                        <?= statusIcon($k['status']) ?>
                        <?= statusLabel($k['status']) ?>
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron-icon"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
