<?php
/**
 * admin/pesan.php - Halaman Pesan Masuk
 */

require_once 'auth.php';

$pageTitle  = 'Pesan Masuk';
$activePage = 'pesan';

require_once 'layout.php';

if (isset($_GET['baca']) && is_numeric($_GET['baca'])) {
    $bacaId = (int)$_GET['baca'];
    $pdo->prepare("UPDATE pesan SET is_read = 1 WHERE id = :id")->execute([':id' => $bacaId]);
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM pesan WHERE id = :id")->execute([':id' => $hapusId]);
    header('Location: pesan.php?msg=hapus');
    exit;
}

if (isset($_GET['baca_semua'])) {
    $pdo->exec("UPDATE pesan SET is_read = 1");
    header('Location: pesan.php?msg=baca_semua');
    exit;
}

$filter   = $_GET['filter'] ?? 'semua';
$sqlWhere = '';
if ($filter === 'belum') {
    $sqlWhere = 'WHERE is_read = 0';
} elseif ($filter === 'sudah') {
    $sqlWhere = 'WHERE is_read = 1';
}

$pesanList  = $pdo->query("SELECT id, nama, email, subjek, isi, tanggal, is_read FROM pesan {$sqlWhere} ORDER BY tanggal DESC")->fetchAll();
$totalBelum = (int)$pdo->query("SELECT COUNT(*) FROM pesan WHERE is_read = 0")->fetchColumn();
$total      = (int)$pdo->query("SELECT COUNT(*) FROM pesan")->fetchColumn();
$msg        = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'hapus'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Pesan berhasil dihapus.
</div>
<?php elseif ($msg === 'baca_semua'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Semua pesan ditandai sudah dibaca.
</div>
<?php endif; ?>

<!-- Header -->
<div class="pesan-toolbar">
    <div>
        <h2 class="pesan-toolbar-title">Pesan Masuk dari Masyarakat</h2>
        <p class="pesan-toolbar-meta">
            Total: <strong><?= $total ?></strong> pesan &nbsp;|&nbsp;
            Belum dibaca: <strong class="<?= $totalBelum > 0 ? 'text-danger' : 'text-success' ?>"><?= $totalBelum ?></strong>
        </p>
    </div>
    <div class="pesan-toolbar-actions">
        <a href="?filter=semua" class="btn-admin <?= $filter === 'semua' ? 'btn-admin-primary' : 'btn-admin-secondary' ?> btn-admin-sm">Semua</a>
        <a href="?filter=belum" class="btn-admin <?= $filter === 'belum' ? 'btn-admin-primary' : 'btn-admin-secondary' ?> btn-admin-sm">Belum Dibaca (<?= $totalBelum ?>)</a>
        <a href="?filter=sudah" class="btn-admin <?= $filter === 'sudah' ? 'btn-admin-primary' : 'btn-admin-secondary' ?> btn-admin-sm">Sudah Dibaca</a>
        <?php if ($totalBelum > 0): ?>
        <a href="?baca_semua=1" class="btn-admin btn-admin-secondary btn-admin-sm"
           data-confirm="Tandai semua pesan sebagai sudah dibaca?">Tandai Semua Dibaca</a>
        <?php endif; ?>
    </div>
</div>

<!-- Daftar Pesan -->
<?php if (empty($pesanList)): ?>
<div class="pesan-empty">
    <div class="pesan-empty-icon" aria-hidden="true">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
    <h3 class="pesan-empty-title">Belum ada pesan masuk</h3>
    <p class="pesan-empty-desc">Pesan dari masyarakat akan tampil di sini.</p>
</div>
<?php else: ?>

<?php foreach ($pesanList as $p): ?>
<div class="msg-card <?= !$p['is_read'] ? 'unread' : '' ?>" id="pesan-<?= $p['id'] ?>">

    <!-- Header Pesan -->
    <div class="msg-card-header">
        <div class="msg-sender">
            <div class="sender-avatar" aria-hidden="true">
                <?= strtoupper(substr($p['nama'], 0, 1)) ?>
            </div>
            <div>
                <div class="sender-name"><?= htmlspecialchars($p['nama']) ?></div>
                <div class="sender-email"><?= htmlspecialchars($p['email']) ?></div>
            </div>
        </div>
        <div class="msg-card-meta-wrap">
            <span class="msg-meta"><?= date('d M Y, H:i', strtotime($p['tanggal'])) ?></span>
            <?php if (!$p['is_read']): ?>
            <span class="status-badge status-badge--new">Baru</span>
            <?php else: ?>
            <span class="status-badge status-badge--read">Dibaca</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Subjek -->
    <div class="msg-subject">
        <?= htmlspecialchars($p['subjek']) ?>
    </div>

    <!-- Isi -->
    <div class="msg-body">
        <?= nl2br(htmlspecialchars($p['isi'])) ?>
    </div>

    <!-- Aksi -->
    <div class="msg-card-footer">
        <a href="mailto:<?= htmlspecialchars($p['email']) ?>?subject=Re: <?= htmlspecialchars(rawurlencode($p['subjek'])) ?>"
           class="btn-admin btn-admin-secondary btn-admin-sm"
           id="btn-balas-<?= $p['id'] ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
            Balas via Email
        </a>
        <div class="td-actions">
            <?php if (!$p['is_read']): ?>
            <a href="?baca=<?= $p['id'] ?>&filter=<?= $filter ?>"
               class="btn-admin btn-admin-secondary btn-admin-sm"
               id="btn-baca-<?= $p['id'] ?>">Tandai Dibaca</a>
            <?php endif; ?>
            <a href="pesan.php?hapus=<?= $p['id'] ?>"
               class="btn-admin btn-admin-danger btn-admin-sm"
               id="btn-hapus-pesan-<?= $p['id'] ?>"
               data-confirm="Yakin ingin menghapus pesan dari <?= htmlspecialchars($p['nama']) ?>?">Hapus</a>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
