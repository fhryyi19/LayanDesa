<?php
/**
 * admin/pengumuman.php - Daftar Pengumuman (Admin)
 */

require_once 'auth.php';

$pageTitle  = 'Kelola Pengumuman';
$activePage = 'pengumuman';

require_once 'layout.php';

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM pengumuman WHERE id = :id");
    $stmt->execute([':id' => $hapusId]);
    header('Location: pengumuman.php?msg=hapus');
    exit;
}

$list = $pdo->query("SELECT id, judul, tanggal FROM pengumuman ORDER BY tanggal DESC")->fetchAll();
$msg  = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'tambah'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Pengumuman berhasil ditambahkan.
</div>
<?php elseif ($msg === 'edit'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Pengumuman berhasil diperbarui.
</div>
<?php elseif ($msg === 'hapus'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Pengumuman berhasil dihapus.
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Daftar Pengumuman</span>
        <div class="table-actions">
            <div class="search-box">
                <span class="search-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" id="tableSearch" placeholder="Cari pengumuman...">
            </div>
            <a href="pengumuman-tambah.php" class="btn-admin btn-admin-primary" id="btn-tambah-pengumuman">
                + Tambah Pengumuman
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-num">#</th>
                    <th>Judul Pengumuman</th>
                    <th class="th-date">Tanggal</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="4" class="td-empty">
                        Belum ada pengumuman. <a href="pengumuman-tambah.php">Tambah sekarang</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($list as $i => $pgm): ?>
                <tr>
                    <td class="td-number"><?= $i + 1 ?></td>
                    <td class="td-title"><?= htmlspecialchars($pgm['judul']) ?></td>
                    <td class="td-date"><?= date('d M Y', strtotime($pgm['tanggal'])) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="pengumuman-edit.php?id=<?= $pgm['id'] ?>"
                               class="btn-admin btn-admin-warning btn-admin-sm"
                               id="btn-edit-pgm-<?= $pgm['id'] ?>">Edit</a>
                            <a href="pengumuman.php?hapus=<?= $pgm['id'] ?>"
                               class="btn-admin btn-admin-danger btn-admin-sm"
                               id="btn-hapus-pgm-<?= $pgm['id'] ?>"
                               data-confirm="Yakin ingin menghapus pengumuman '<?= htmlspecialchars($pgm['judul']) ?>'?">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-footer-info">
        Total: <strong><?= count($list) ?></strong> pengumuman
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
