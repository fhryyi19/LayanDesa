<?php
/**
 * admin/berita.php - Daftar Berita (Admin)
 */

require_once 'auth.php';

$pageTitle  = 'Kelola Berita';
$activePage = 'berita';

require_once 'layout.php';

// Proses hapus berita
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = :id");
    $stmt->execute([':id' => $hapusId]);
    header('Location: berita.php?msg=hapus');
    exit;
}

$beritaList = $pdo->query("SELECT id, judul, tanggal FROM berita ORDER BY tanggal DESC")->fetchAll();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'tambah'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Berita berhasil ditambahkan.
</div>
<?php elseif ($msg === 'edit'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Berita berhasil diperbarui.
</div>
<?php elseif ($msg === 'hapus'): ?>
<div class="admin-alert admin-alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    Berita berhasil dihapus.
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Daftar Berita</span>
        <div class="table-actions">
            <div class="search-box">
                <span class="search-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" id="tableSearch" placeholder="Cari berita...">
            </div>
            <a href="berita-tambah.php" class="btn-admin btn-admin-primary" id="btn-tambah-berita">
                + Tambah Berita
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-num">#</th>
                    <th>Judul Berita</th>
                    <th class="th-date">Tanggal</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($beritaList)): ?>
                <tr>
                    <td colspan="4" class="td-empty">
                        Belum ada berita. <a href="berita-tambah.php">Tambah sekarang</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($beritaList as $i => $b): ?>
                <tr>
                    <td class="td-number"><?= $i + 1 ?></td>
                    <td class="td-title"><?= htmlspecialchars($b['judul']) ?></td>
                    <td class="td-date"><?= date('d M Y', strtotime($b['tanggal'])) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="../berita-detail.php?id=<?= $b['id'] ?>"
                               target="_blank"
                               class="btn-admin btn-admin-secondary btn-admin-sm"
                               id="btn-lihat-berita-<?= $b['id'] ?>"
                               title="Lihat">Lihat</a>
                            <a href="berita-edit.php?id=<?= $b['id'] ?>"
                               class="btn-admin btn-admin-warning btn-admin-sm"
                               id="btn-edit-berita-<?= $b['id'] ?>"
                               title="Edit">Edit</a>
                            <a href="berita.php?hapus=<?= $b['id'] ?>"
                               class="btn-admin btn-admin-danger btn-admin-sm"
                               id="btn-hapus-berita-<?= $b['id'] ?>"
                               title="Hapus"
                               data-confirm="Yakin ingin menghapus berita '<?= htmlspecialchars($b['judul']) ?>'?">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-footer-info">
        Total: <strong><?= count($beritaList) ?></strong> berita
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
