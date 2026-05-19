<?php
/**
 * admin/pengumuman-tambah.php - Tambah Pengumuman Baru
 */

require_once 'auth.php';

$pageTitle  = 'Tambah Pengumuman';
$activePage = 'pengumuman';

require_once 'layout.php';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = trim($_POST['judul']   ?? '');
    $isi     = trim($_POST['isi']     ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');

    $old = compact('judul', 'isi', 'tanggal');

    if (empty($judul))        $errors['judul']   = 'Judul tidak boleh kosong.';
    if (strlen($judul) > 255) $errors['judul']   = 'Judul maksimal 255 karakter.';
    if (empty($isi))          $errors['isi']     = 'Isi pengumuman tidak boleh kosong.';
    if (empty($tanggal))      $errors['tanggal'] = 'Tanggal tidak boleh kosong.';

    $gambarName = null;

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
                $gambarName = $newFileName;
            } else {
                $errors['gambar'] = 'Gagal mengupload gambar.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi, gambar, tanggal) VALUES (:judul, :isi, :gambar, :tanggal)");
        $stmt->execute([':judul' => $judul, ':isi' => $isi, ':gambar' => $gambarName, ':tanggal' => $tanggal]);
        header('Location: pengumuman.php?msg=tambah');
        exit;
    }
}
?>

<div class="btn-back-wrap">
    <a href="pengumuman.php" class="btn-admin btn-admin-secondary btn-admin-sm">&larr; Kembali ke Daftar Pengumuman</a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <h2>Tambah Pengumuman Baru</h2>
    </div>
    <div class="form-card-body">
        <form class="admin-form" method="POST" action="pengumuman-tambah.php" enctype="multipart/form-data" data-validate>

            <div class="form-group">
                <label for="judul">Judul Pengumuman <span class="req">*</span></label>
                <input type="text" id="judul" name="judul"
                       placeholder="Masukkan judul pengumuman"
                       value="<?= htmlspecialchars($old['judul'] ?? '') ?>"
                       maxlength="255" required>
                <?php if (isset($errors['judul'])): ?>
                <div class="field-error"><?= $errors['judul'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Pengumuman <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal"
                       value="<?= htmlspecialchars($old['tanggal'] ?? date('Y-m-d')) ?>" required>
                <?php if (isset($errors['tanggal'])): ?>
                <div class="field-error"><?= $errors['tanggal'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar/Foto Pengumuman <span class="label-optional">(Opsional)</span></label>
                <input type="file" id="gambar" name="gambar"
                       accept="image/jpeg, image/png, image/webp"
                       class="file-input">
                <div class="field-hint">Format yang didukung: JPG, PNG, WEBP. Ukuran maksimal 5MB.</div>
                <?php if (isset($errors['gambar'])): ?>
                <div class="field-error"><?= $errors['gambar'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman <span class="req">*</span></label>
                <textarea id="isi" name="isi" rows="10"
                          placeholder="Tulis isi pengumuman di sini..."
                          required><?= htmlspecialchars($old['isi'] ?? '') ?></textarea>
                <?php if (isset($errors['isi'])): ?>
                <div class="field-error"><?= $errors['isi'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="pengumuman.php" class="btn-admin btn-admin-secondary">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary" id="btn-simpan-pgm">
                    Simpan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout_end.php'; ?>
