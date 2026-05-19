<?php
/**
 * admin/keluhan-detail.php - Detail & Kelola Keluhan
 */

require_once 'auth.php';
require_once '../auth-masyarakat.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: keluhan.php'); exit; }

$pageTitle  = 'Detail Keluhan';
$activePage = 'keluhan';

require_once 'layout.php';

// Ambil data keluhan
$stmt = $pdo->prepare("SELECT k.*, u.nama_lengkap, u.email, u.no_hp FROM keluhan k JOIN users u ON k.user_id = u.id WHERE k.id = ?");
$stmt->execute([$id]);
$keluhan = $stmt->fetch();
if (!$keluhan) { echo '<p style="padding:2rem">Keluhan tidak ditemukan.</p>'; require_once 'layout_end.php'; exit; }

// Ambil timeline
$tStmt = $pdo->prepare("SELECT t.*, a.username as admin_nama FROM tanggapan_keluhan t LEFT JOIN admin a ON t.admin_id = a.id WHERE t.keluhan_id = ? ORDER BY t.created_at ASC");
$tStmt->execute([$id]);
$timeline = $tStmt->fetchAll();

$alertMsg  = '';
$alertType = '';

// ── PROSES AKSI ADMIN ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update Status
    if ($action === 'update_status') {
        $newStatus     = $_POST['new_status'] ?? '';
        $pesan         = trim($_POST['pesan'] ?? '');
        $alasan        = trim($_POST['alasan_penolakan'] ?? '');
        $allowedStatus = ['menunggu','diterima','diproses','selesai','ditolak'];

        if (!in_array($newStatus, $allowedStatus)) {
            $alertMsg = 'Status tidak valid.'; $alertType = 'danger';
        } elseif (empty($pesan)) {
            $alertMsg = 'Pesan keterangan tidak boleh kosong.'; $alertType = 'danger';
        } else {
            try {
                $pdo->beginTransaction();

                // Update status keluhan
                $updFields = "status = ?";
                $updParams = [$newStatus];
                if ($newStatus === 'ditolak') {
                    $updFields .= ", alasan_penolakan = ?";
                    $updParams[] = $alasan ?: 'Tidak memenuhi persyaratan.';
                } else {
                    $updFields .= ", alasan_penolakan = NULL";
                }
                $updParams[] = $id;
                $pdo->prepare("UPDATE keluhan SET {$updFields} WHERE id = ?")->execute($updParams);

                // Log timeline
                $pdo->prepare("INSERT INTO tanggapan_keluhan (keluhan_id, admin_id, jenis, status_baru, pesan) VALUES (?, ?, 'status_update', ?, ?)")
                    ->execute([$id, $_SESSION['admin_id'], $newStatus, $pesan]);

                $pdo->commit();

                // Refresh data
                $stmt->execute([$id]);
                $keluhan   = $stmt->fetch();
                $tStmt->execute([$id]);
                $timeline  = $tStmt->fetchAll();

                $alertMsg  = 'Status keluhan berhasil diperbarui.';
                $alertType = 'success';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $alertMsg = 'Gagal memperbarui status: ' . $e->getMessage(); $alertType = 'danger';
            }
        }
    }

    // Tambah Tanggapan
    if ($action === 'tambah_tanggapan') {
        $pesan = trim($_POST['pesan_tanggapan'] ?? '');
        if (empty($pesan)) {
            $alertMsg = 'Isi tanggapan tidak boleh kosong.'; $alertType = 'danger';
        } else {
            try {
                $pdo->prepare("INSERT INTO tanggapan_keluhan (keluhan_id, admin_id, jenis, pesan) VALUES (?, ?, 'tanggapan', ?)")
                    ->execute([$id, $_SESSION['admin_id'], $pesan]);
                $tStmt->execute([$id]);
                $timeline  = $tStmt->fetchAll();
                $alertMsg  = 'Tanggapan berhasil ditambahkan.';
                $alertType = 'success';
            } catch (PDOException $e) {
                $alertMsg = 'Gagal menambahkan tanggapan.'; $alertType = 'danger';
            }
        }
    }
}
?>

<!-- Back Button -->
<div style="margin-bottom:1.5rem;">
    <a href="keluhan.php" class="btn-admin btn-admin-secondary btn-admin-sm">← Kembali ke Daftar</a>
</div>

<?php if ($alertMsg): ?>
<div class="alert-admin alert-admin--<?= $alertType ?>" role="alert" style="margin-bottom:1.5rem;">
    <?= htmlspecialchars($alertMsg) ?>
</div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 380px; gap:1.5rem; align-items:start;">

    <!-- ── KIRI: Info Keluhan ── -->
    <div>
        <div class="table-card" style="margin-bottom:1.5rem;">
            <div class="table-card-header">
                <div>
                    <span class="kode-tiket-badge" style="font-size:0.9rem;"><?= htmlspecialchars($keluhan['kode_tiket']) ?></span>
                    <h2 style="margin:0.5rem 0 0; font-size:1.2rem; color:var(--text-primary);"><?= htmlspecialchars($keluhan['judul']) ?></h2>
                </div>
                <span class="admin-status-badge admin-status-badge--<?= $keluhan['status'] ?> admin-status-badge--lg">
                    <?= statusLabel($keluhan['status']) ?>
                </span>
            </div>

            <!-- Meta Info -->
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; padding:1.25rem 1.5rem; border-top:1px solid var(--border); border-bottom:1px solid var(--border); background:var(--bg-subtle);">
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Pelapor</div><strong><?= htmlspecialchars($keluhan['nama_lengkap']) ?></strong></div>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Email</div><?= htmlspecialchars($keluhan['email']) ?></div>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">No. HP</div><?= htmlspecialchars($keluhan['no_hp']) ?></div>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Kategori</div><?= htmlspecialchars($keluhan['kategori']) ?></div>
                <?php if ($keluhan['lokasi']): ?>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Lokasi</div><?= htmlspecialchars($keluhan['lokasi']) ?></div>
                <?php endif; ?>
                <?php if ($keluhan['tanggal_kejadian']): ?>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Tgl Kejadian</div><?= formatTanggal($keluhan['tanggal_kejadian']) ?></div>
                <?php endif; ?>
                <div><div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Tgl Lapor</div><?= formatTanggalWaktu($keluhan['created_at']) ?></div>
            </div>

            <!-- Isi -->
            <div style="padding:1.5rem;">
                <div style="font-size:.8rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem;">Isi Laporan</div>
                <div style="line-height:1.8; color:var(--text-primary); white-space:pre-wrap;"><?= htmlspecialchars($keluhan['isi']) ?></div>

                <?php if ($keluhan['foto_bukti']): ?>
                <div style="margin-top:1.25rem;">
                    <div style="font-size:.8rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem;">Foto Bukti</div>
                    <a href="../<?= htmlspecialchars($keluhan['foto_bukti']) ?>" target="_blank" rel="noopener">
                        <img src="../<?= htmlspecialchars($keluhan['foto_bukti']) ?>" alt="Foto bukti" style="max-width:100%;max-height:300px;border-radius:8px;border:1px solid var(--border);cursor:pointer;">
                    </a>
                </div>
                <?php endif; ?>

                <?php if ($keluhan['status']==='ditolak' && $keluhan['alasan_penolakan']): ?>
                <div style="margin-top:1rem; padding:1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; color:#991b1b;">
                    <strong>Alasan Penolakan:</strong><br><?= nl2br(htmlspecialchars($keluhan['alasan_penolakan'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Timeline -->
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">Timeline Penanganan</span>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                <?php if (empty($timeline)): ?>
                <p style="color:var(--text-muted); text-align:center; padding:1rem 0;">Belum ada aktivitas.</p>
                <?php else: ?>
                <div class="admin-timeline">
                    <?php foreach ($timeline as $t): ?>
                    <div class="admin-timeline-item admin-tl-<?= $t['jenis'] ?>">
                        <div class="admin-tl-dot"></div>
                        <div class="admin-tl-content">
                            <?php if ($t['status_baru']): ?>
                            <span class="admin-status-badge admin-status-badge--<?= $t['status_baru'] ?>" style="font-size:.72rem; margin-bottom:.4rem; display:inline-flex;">
                                <?= statusLabel($t['status_baru']) ?>
                            </span>
                            <?php endif; ?>
                            <div style="color:var(--text-primary); line-height:1.6; margin-bottom:.4rem;"><?= nl2br(htmlspecialchars($t['pesan'])) ?></div>
                            <div style="font-size:.75rem; color:var(--text-muted);">
                                <?php if ($t['admin_nama']): ?><span>Petugas: <?= htmlspecialchars($t['admin_nama']) ?> &bull; </span><?php endif; ?>
                                <span><?= formatTanggalWaktu($t['created_at']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Form Tambah Tanggapan -->
                <form method="POST" action="keluhan-detail.php?id=<?= $id ?>" style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <input type="hidden" name="action" value="tambah_tanggapan">
                    <label style="font-size:.85rem; font-weight:600; color:var(--text-primary); display:block; margin-bottom:.5rem;">Tambah Tanggapan</label>
                    <textarea name="pesan_tanggapan" rows="3" placeholder="Tulis tanggapan atau keterangan tambahan..." class="admin-textarea" id="textarea-tanggapan" required></textarea>
                    <button type="submit" class="btn-admin btn-admin-primary btn-admin-sm" style="margin-top:.75rem;" id="btn-kirim-tanggapan">Kirim Tanggapan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── KANAN: Update Status ── -->
    <div>
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">Update Status</span>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                <form method="POST" action="keluhan-detail.php?id=<?= $id ?>" id="formUpdateStatus">
                    <input type="hidden" name="action" value="update_status">

                    <div style="margin-bottom:1rem;">
                        <label style="font-size:.85rem; font-weight:600; color:var(--text-primary); display:block; margin-bottom:.5rem;">Status Baru <span style="color:#e53e3e">*</span></label>
                        <select name="new_status" class="admin-select" id="select-new-status" required>
                            <?php foreach (['menunggu'=>'Menunggu Verifikasi','diterima'=>'Diterima','diproses'=>'Sedang Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $keluhan['status']===$v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-bottom:1rem;" id="alasan-wrapper" style="display:none;">
                        <label style="font-size:.85rem; font-weight:600; color:var(--text-primary); display:block; margin-bottom:.5rem;">Alasan Penolakan</label>
                        <textarea name="alasan_penolakan" rows="3" placeholder="Tuliskan alasan penolakan..." class="admin-textarea" id="textarea-alasan"><?= htmlspecialchars($keluhan['alasan_penolakan'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label style="font-size:.85rem; font-weight:600; color:var(--text-primary); display:block; margin-bottom:.5rem;">Keterangan <span style="color:#e53e3e">*</span></label>
                        <textarea name="pesan" rows="4" placeholder="Keterangan perubahan status (akan ditampilkan di timeline)..." class="admin-textarea" id="textarea-keterangan" required></textarea>
                    </div>

                    <button type="submit" class="btn-admin btn-admin-primary" style="width:100%;" id="btn-update-status">Update Status</button>
                </form>

                <!-- Status Progress Visual -->
                <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <div style="font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;">Alur Status</div>
                    <?php
                    $flow = ['menunggu','diterima','diproses','selesai'];
                    $cur  = $keluhan['status'];
                    $curIdx = array_search($cur, $flow);
                    ?>
                    <?php foreach ($flow as $i => $s): ?>
                    <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:.6rem;">
                        <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0;
                            background: <?= ($curIdx !== false && $i <= $curIdx) ? 'var(--primary)' : 'var(--bg-subtle)' ?>;
                            color: <?= ($curIdx !== false && $i <= $curIdx) ? '#fff' : 'var(--text-muted)' ?>;
                            border: 2px solid <?= ($cur === $s) ? 'var(--primary)' : 'var(--border)' ?>;">
                            <?= $i + 1 ?>
                        </div>
                        <span style="font-size:.85rem; font-weight:<?= $cur===$s ? '700' : '400' ?>; color:<?= $cur===$s ? 'var(--primary)' : 'var(--text-secondary)' ?>;">
                            <?= statusLabel($s) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($cur === 'ditolak'): ?>
                    <div style="display:flex; align-items:center; gap:.75rem; margin-top:.6rem;">
                        <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0; background:#fee2e2; color:#ef4444; border:2px solid #ef4444;">✕</div>
                        <span style="font-size:.85rem; font-weight:700; color:#ef4444;">Ditolak</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide alasan penolakan
const statusSel    = document.getElementById('select-new-status');
const alasanWrap   = document.getElementById('alasan-wrapper');
function toggleAlasan() {
    if (statusSel && alasanWrap) {
        alasanWrap.style.display = statusSel.value === 'ditolak' ? 'block' : 'none';
    }
}
if (statusSel) { statusSel.addEventListener('change', toggleAlasan); toggleAlasan(); }
</script>

<?php require_once 'layout_end.php'; ?>
