<?php
/**
 * admin/keluhan.php - Kelola Keluhan Masyarakat
 */

require_once 'auth.php';

$pageTitle  = 'Keluhan Masyarakat';
$activePage = 'keluhan';

require_once 'layout.php';
require_once '../auth-masyarakat.php';

// Filter
$filter   = $_GET['status'] ?? '';
$search   = trim($_GET['q'] ?? '');
$allowed  = ['','menunggu','diterima','diproses','selesai','ditolak'];
if (!in_array($filter, $allowed)) $filter = '';

// Query
$sql    = "SELECT k.id, k.kode_tiket, k.judul, k.kategori, k.status, k.created_at, u.nama_lengkap 
           FROM keluhan k JOIN users u ON k.user_id = u.id WHERE 1=1";
$params = [];
if ($filter !== '') { $sql .= " AND k.status = ?"; $params[] = $filter; }
if ($search !== '')  { $sql .= " AND (k.judul LIKE ? OR k.kode_tiket LIKE ? OR u.nama_lengkap LIKE ?)"; $s = "%{$search}%"; $params[] = $s; $params[] = $s; $params[] = $s; }
$sql .= " ORDER BY FIELD(k.status,'menunggu','diterima','diproses','selesai','ditolak'), k.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

// Stats
$statsRaw = $pdo->query("SELECT status, COUNT(*) as cnt FROM keluhan GROUP BY status")->fetchAll();
$stats = ['menunggu'=>0,'diterima'=>0,'diproses'=>0,'selesai'=>0,'ditolak'=>0,'total'=>0];
foreach ($statsRaw as $s) { $stats[$s['status']] = (int)$s['cnt']; $stats['total'] += (int)$s['cnt']; }
?>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
    <div class="stat-card"><div class="stat-card-icon blue" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/></svg></div><div class="stat-card-info"><span class="stat-value"><?= $stats['total'] ?></span><span class="stat-label">Total</span></div></div>
    <div class="stat-card"><div class="stat-card-icon gold" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="stat-card-info"><span class="stat-value"><?= $stats['menunggu'] ?></span><span class="stat-label">Menunggu</span></div></div>
    <div class="stat-card"><div class="stat-card-icon blue" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg></div><div class="stat-card-info"><span class="stat-value"><?= $stats['diproses'] ?></span><span class="stat-label">Diproses</span></div></div>
    <div class="stat-card"><div class="stat-card-icon green" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div class="stat-card-info"><span class="stat-value"><?= $stats['selesai'] ?></span><span class="stat-label">Selesai</span></div></div>
    <div class="stat-card"><div class="stat-card-icon red" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div><div class="stat-card-info"><span class="stat-value"><?= $stats['ditolak'] ?></span><span class="stat-label">Ditolak</span></div></div>
</div>

<!-- Filter & Search -->
<div class="table-card">
    <div class="table-card-header" style="flex-wrap:wrap; gap:12px;">
        <span class="table-card-title">Daftar Keluhan Masyarakat</span>
        <form method="GET" action="keluhan.php" class="keluhan-filter-form">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari keluhan, kode, nama..." class="admin-search-input" id="input-search-keluhan">
            <select name="status" class="admin-filter-select" id="select-filter-status">
                <option value="">Semua Status</option>
                <?php foreach (['menunggu'=>'Menunggu','diterima'=>'Diterima','diproses'=>'Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= $filter===$v?'selected':'' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-admin btn-admin-primary btn-admin-sm" id="btn-filter-keluhan">Filter</button>
            <?php if ($filter||$search): ?><a href="keluhan.php" class="btn-admin btn-admin-secondary btn-admin-sm">Reset</a><?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode Tiket</th>
                    <th>Pelapor</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr><td colspan="7" class="td-empty">Tidak ada keluhan ditemukan.</td></tr>
                <?php else: ?>
                <?php foreach ($list as $k): ?>
                <tr class="<?= $k['status']==='menunggu' ? 'tr-highlight-new' : '' ?>">
                    <td><span class="kode-tiket-badge"><?= htmlspecialchars($k['kode_tiket']) ?></span></td>
                    <td><?= htmlspecialchars($k['nama_lengkap']) ?></td>
                    <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($k['judul']) ?>"><?= htmlspecialchars($k['judul']) ?></td>
                    <td><span class="kat-badge"><?= htmlspecialchars($k['kategori']) ?></span></td>
                    <td>
                        <span class="admin-status-badge admin-status-badge--<?= $k['status'] ?>">
                            <?= statusLabel($k['status']) ?>
                        </span>
                    </td>
                    <td class="td-date"><?= date('d/m/Y', strtotime($k['created_at'])) ?></td>
                    <td>
                        <a href="keluhan-detail.php?id=<?= $k['id'] ?>" class="btn-admin btn-admin-primary btn-admin-sm" id="btn-detail-<?= $k['id'] ?>">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
