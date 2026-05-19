<?php
/**
 * admin/berita-edit.php - Edit Berita yang Ada
 */

require_once 'auth.php';

$pageTitle  = 'Edit Berita';
$activePage = 'berita';

require_once 'layout.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: berita.php'); exit; }

$stmt = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal FROM berita WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$berita = $stmt->fetch();

if (!$berita) {
    echo '<div class="admin-alert admin-alert-danger">Berita tidak ditemukan.</div>';
    require_once 'layout_end.php';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = trim($_POST['judul']   ?? '');
    $isi     = trim($_POST['isi']     ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');

    $berita['judul']   = $judul;
    $berita['isi']     = $isi;
    $berita['tanggal'] = $tanggal;

    if (empty($judul))        $errors['judul']   = 'Judul tidak boleh kosong.';
    if (strlen($judul) > 255) $errors['judul']   = 'Judul maksimal 255 karakter.';
    if (empty($isi))          $errors['isi']     = 'Isi berita tidak boleh kosong.';
    if (empty($tanggal))      $errors['tanggal'] = 'Tanggal tidak boleh kosong.';

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
            $newFileName = uniqid('berita_', true) . '.' . $fileExt;
            $uploadDir   = '../uploads/berita/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                $gambarUpdateQuery  = ", gambar = :gambar";
                $gambarUpdateParams = [':gambar' => $newFileName];
                if (!empty($berita['gambar']) && file_exists($uploadDir . $berita['gambar'])) {
                    unlink($uploadDir . $berita['gambar']);
                }
            } else {
                $errors['gambar'] = 'Gagal mengupload gambar baru.';
            }
        }
    } elseif (isset($_POST['hapus_gambar']) && $_POST['hapus_gambar'] == '1') {
        $gambarUpdateQuery = ", gambar = NULL";
        $uploadDir = '../uploads/berita/';
        if (!empty($berita['gambar']) && file_exists($uploadDir . $berita['gambar'])) {
            unlink($uploadDir . $berita['gambar']);
        }
    }

    if (empty($errors)) {
        $queryParams = [':judul' => $judul, ':isi' => $isi, ':tanggal' => $tanggal, ':id' => $id];
        $queryParams = array_merge($queryParams, $gambarUpdateParams);
        $stmtUp = $pdo->prepare("UPDATE berita SET judul = :judul, isi = :isi, tanggal = :tanggal" . $gambarUpdateQuery . " WHERE id = :id");
        $stmtUp->execute($queryParams);
        header('Location: berita.php?msg=edit');
        exit;
    }
}
?>

<div class="btn-back-wrap">
    <a href="berita.php" class="btn-admin btn-admin-secondary btn-admin-sm">&larr; Kembali ke Daftar Berita</a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <h2>Edit Berita</h2>
    </div>
    <div class="form-card-body">

        <form class="admin-form" method="POST" action="berita-edit.php?id=<?= $id ?>" enctype="multipart/form-data" data-validate>

            <div class="form-group">
                <label for="judul">Judul Berita <span class="req">*</span></label>
                <input type="text" id="judul" name="judul"
                       placeholder="Judul berita"
                       value="<?= htmlspecialchars($berita['judul']) ?>"
                       maxlength="255" required>
                <?php if (isset($errors['judul'])): ?>
                <div class="field-error"><?= $errors['judul'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Publikasi <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal"
                       value="<?= htmlspecialchars($berita['tanggal']) ?>" required>
                <?php if (isset($errors['tanggal'])): ?>
                <div class="field-error"><?= $errors['tanggal'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar/Foto Berita</label>
                <?php if (!empty($berita['gambar'])): ?>
                <div class="img-preview-wrap">
                    <img src="../uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>"
                         alt="Gambar Berita"
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
                <label for="isi">Isi Berita <span class="req">*</span></label>
                <textarea id="isi" name="isi" rows="12"
                          placeholder="Isi berita..." required><?= htmlspecialchars($berita['isi']) ?></textarea>
                <?php if (isset($errors['isi'])): ?>
                <div class="field-error"><?= $errors['isi'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="berita.php" class="btn-admin btn-admin-secondary" id="btn-batal-edit-berita">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary" id="btn-update-berita">
                    Perbarui Berita
                </button>
            </div>

        </form>
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
