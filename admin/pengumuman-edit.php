<?php
/**
 * admin/pengumuman-edit.php - Edit Pengumuman
 */

require_once 'auth.php';

$pageTitle  = 'Edit Pengumuman';
$activePage = 'pengumuman';

require_once 'layout.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: pengumuman.php'); exit; }

$stmt = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal FROM pengumuman WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$pgm = $stmt->fetch();

if (!$pgm) {
    echo '<div class="admin-alert admin-alert-danger">Pengumuman tidak ditemukan.</div>';
    require_once 'layout_end.php';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = trim($_POST['judul']   ?? '');
    $isi     = trim($_POST['isi']     ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');

    $pgm['judul']   = $judul;
    $pgm['isi']     = $isi;
    $pgm['tanggal'] = $tanggal;

    if (empty($judul))   $errors['judul']   = 'Judul tidak boleh kosong.';
    if (empty($isi))     $errors['isi']     = 'Isi pengumuman tidak boleh kosong.';
    if (empty($tanggal)) $errors['tanggal'] = 'Tanggal tidak boleh kosong.';

    $gambarUpdateQuery  = "";
    $gambarUpdateParams = [];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $tmpName  = $_FILES['gambar']['tmp_name'];
        $fileName = basename($_FILES['gambar']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExt, $allowed)) {
            $errors['gambar'] = 'Format gambar hanya boleh JPG, JPEG, PNG, atau WEBP.';
        } else {
            $newFileName = uniqid('pengumuman_', true) . '.' . $fileExt;
            $uploadDir   = '../uploads/pengumuman/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                $gambarUpdateQuery  = ", gambar = :gambar";
                $gambarUpdateParams = [':gambar' => $newFileName];
                if (!empty($pgm['gambar']) && file_exists($uploadDir . $pgm['gambar'])) {
                    unlink($uploadDir . $pgm['gambar']);
                }
            } else {
                $errors['gambar'] = 'Gagal mengupload gambar baru.';
            }
        }
    } elseif (isset($_POST['hapus_gambar']) && $_POST['hapus_gambar'] == '1') {
        $gambarUpdateQuery = ", gambar = NULL";
        $uploadDir = '../uploads/pengumuman/';
        if (!empty($pgm['gambar']) && file_exists($uploadDir . $pgm['gambar'])) {
            unlink($uploadDir . $pgm['gambar']);
        }
    }

    if (empty($errors)) {
        $queryParams = [':judul' => $judul, ':isi' => $isi, ':tanggal' => $tanggal, ':id' => $id];
        $queryParams = array_merge($queryParams, $gambarUpdateParams);
        $stmtUp = $pdo->prepare("UPDATE pengumuman SET judul = :judul, isi = :isi, tanggal = :tanggal" . $gambarUpdateQuery . " WHERE id = :id");
        $stmtUp->execute($queryParams);
        header('Location: pengumuman.php?msg=edit');
        exit;
    }
}
?>

<div class="btn-back-wrap">
    <a href="pengumuman.php" class="btn-admin btn-admin-secondary btn-admin-sm">&larr; Kembali ke Daftar Pengumuman</a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <h2>Edit Pengumuman</h2>
    </div>
    <div class="form-card-body">
        <form class="admin-form" method="POST" action="pengumuman-edit.php?id=<?= $id ?>" enctype="multipart/form-data" data-validate>

            <div class="form-group">
                <label for="judul">Judul Pengumuman <span class="req">*</span></label>
                <input type="text" id="judul" name="judul"
                       value="<?= htmlspecialchars($pgm['judul']) ?>"
                       maxlength="255" required>
                <?php if (isset($errors['judul'])): ?>
                <div class="field-error"><?= $errors['judul'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Pengumuman <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal"
                       value="<?= htmlspecialchars($pgm['tanggal']) ?>" required>
                <?php if (isset($errors['tanggal'])): ?>
                <div class="field-error"><?= $errors['tanggal'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar/Foto Pengumuman</label>
                <?php if (!empty($pgm['gambar'])): ?>
                <div class="img-preview-wrap">
                    <img src="../uploads/pengumuman/<?= htmlspecialchars($pgm['gambar']) ?>"
                         alt="Gambar Pengumuman"
                         class="img-preview">
                    <div class="img-preview-actions">
                        <label class="hapus-gambar-label">
                            <input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar ini
                        </label>
                    </div>
                </div>
                <?php endif; ?>
                <input type="file" id="gambar" name="gambar"
                       accept="image/jpeg, image/png, image/webp"
                       class="file-input">
                <div class="field-hint">Pilih file baru jika ingin mengganti gambar. Format: JPG, PNG, WEBP.</div>
                <?php if (isset($errors['gambar'])): ?>
                <div class="field-error"><?= $errors['gambar'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman <span class="req">*</span></label>
                <textarea id="isi" name="isi" rows="10"
                          required><?= htmlspecialchars($pgm['isi']) ?></textarea>
                <?php if (isset($errors['isi'])): ?>
                <div class="field-error"><?= $errors['isi'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="pengumuman.php" class="btn-admin btn-admin-secondary">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary" id="btn-update-pgm">
                    Perbarui Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
