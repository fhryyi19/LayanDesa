<?php
/**
 * kontak.php - Halaman Kontak & Sistem Pengaduan Masyarakat
 */

require_once 'config/database.php';
require_once 'auth-masyarakat.php';

$pageTitle  = 'Kontak & Pengaduan';
$activePage = 'kontak';
$basePath   = '';
$metaDesc   = 'Kirim keluhan dan pantau progres penanganan oleh Pemerintah Desa Sukamaju.';

$isLoggedIn = isUserLoggedIn();
$user       = $isLoggedIn ? getCurrentUser() : [];

$alert  = '';
$errors = [];
$old    = [];

// ─── PROSES FORM KELUHAN ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'kirim_keluhan') {
    if (!$isLoggedIn) {
        header('Location: login-masyarakat.php');
        exit;
    }

    $judul            = trim($_POST['judul']            ?? '');
    $kategori         = trim($_POST['kategori']         ?? '');
    $isi              = trim($_POST['isi']              ?? '');
    $lokasi           = trim($_POST['lokasi']           ?? '');
    $tanggal_kejadian = trim($_POST['tanggal_kejadian'] ?? '');

    $old = compact('judul', 'kategori', 'isi', 'lokasi', 'tanggal_kejadian');

    $allowedKategori = ['Infrastruktur','Kebersihan','Keamanan','Pelayanan Publik','Kesehatan','Pendidikan','Sosial','Lainnya'];

    if (empty($judul))                                $errors['judul']    = 'Judul keluhan tidak boleh kosong.';
    elseif (strlen($judul) < 5)                        $errors['judul']    = 'Judul minimal 5 karakter.';
    elseif (strlen($judul) > 255)                      $errors['judul']    = 'Judul maksimal 255 karakter.';

    if (empty($kategori))                             $errors['kategori'] = 'Pilih kategori keluhan.';
    elseif (!in_array($kategori, $allowedKategori))   $errors['kategori'] = 'Kategori tidak valid.';

    if (empty($isi))                                  $errors['isi']      = 'Isi laporan tidak boleh kosong.';
    elseif (strlen($isi) < 20)                         $errors['isi']      = 'Isi laporan minimal 20 karakter.';

    if (!empty($tanggal_kejadian) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kejadian)) {
        $errors['tanggal_kejadian'] = 'Format tanggal tidak valid.';
    }

    // Upload foto
    $fotoBuktiPath = null;
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = handleFotoUpload($_FILES['foto_bukti']);
        if (isset($uploadResult['error'])) {
            $errors['foto_bukti'] = $uploadResult['error'];
        } else {
            $fotoBuktiPath = $uploadResult['path'];
        }
    }

    if (empty($errors)) {
        try {
            $kode = generateKodeTiket($pdo);
            $stmt = $pdo->prepare("INSERT INTO keluhan (user_id, kode_tiket, judul, kategori, isi, foto_bukti, lokasi, tanggal_kejadian, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')");
            $stmt->execute([
                $user['id'], $kode, $judul, $kategori, $isi,
                $fotoBuktiPath,
                $lokasi ?: null,
                $tanggal_kejadian ?: null,
            ]);
            $keluhanId = $pdo->lastInsertId();

            // Log awal ke timeline
            $pdo->prepare("INSERT INTO tanggapan_keluhan (keluhan_id, jenis, status_baru, pesan) VALUES (?, 'sistem', 'menunggu', ?)")
                ->execute([$keluhanId, 'Keluhan berhasil dikirim dan menunggu verifikasi admin.']);

            $old   = [];
            $alert = 'success';
            $alert_kode = $kode;
        } catch (PDOException $e) {
            $alert = 'error';
        }
    }
}

// ─── DATA KELUHAN USER ─────────────────────────────────────────────────
$keluhanUser = [];
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT id, kode_tiket, judul, kategori, status, created_at FROM keluhan WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user['id']]);
    $keluhanUser = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Kontak & Pengaduan</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <span>Kontak & Pengaduan</span>
            </nav>
        </div>
    </div>
</div>

<!-- Konten Utama -->
<section class="content-section">
    <div class="container">

        <?php if ($alert === 'success'): ?>
        <!-- Alert Sukses -->
        <div class="keluhan-success-banner">
            <div class="keluhan-success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="keluhan-success-body">
                <h3>Keluhan Berhasil Dikirim!</h3>
                <p>Nomor tiket Anda: <strong class="tiket-number"><?= htmlspecialchars($alert_kode ?? '') ?></strong></p>
                <p>Simpan nomor tiket ini untuk memantau status keluhan Anda.</p>
                <a href="keluhan-saya.php" class="btn btn-green btn-sm mt-3">Pantau Status Keluhan →</a>
            </div>
        </div>
        <?php elseif ($alert === 'error'): ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>Terjadi kesalahan sistem. Silakan coba lagi.</div>
        </div>
        <?php endif; ?>

        <div class="grid grid-2 kontak-layout">

            <!-- ── KIRI: Form Keluhan / Login Gate ── -->
            <div>
                <div class="kontak-form-intro">
                    <div class="section-badge mb-4">Formulir Pengaduan</div>
                    <h2>Kirim Keluhan</h2>
                    <p>Sampaikan keluhan atau aspirasi Anda kepada Pemerintah Desa Sukamaju. Setiap laporan akan ditangani secara serius.</p>
                </div>

                <?php if (!$isLoggedIn): ?>
                <!-- Login Gate -->
                <div class="login-gate-card">
                    <div class="login-gate-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Login Diperlukan</h3>
                    <p>Silakan login terlebih dahulu untuk mengirim keluhan. Akun diperlukan agar kami dapat menghubungi Anda dan memberikan update status keluhan.</p>
                    <div class="login-gate-actions">
                        <a href="login-masyarakat.php" class="btn btn-green btn-lg" id="btn-login-gate">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Masuk ke Akun
                        </a>
                        <a href="register.php" class="btn btn-outline-green btn-lg" id="btn-register-gate">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                            Daftar Akun Baru
                        </a>
                    </div>
                </div>

                <?php else: ?>
                <!-- Form Keluhan -->
                <form id="formKeluhan" method="POST" action="kontak.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="action" value="kirim_keluhan">

                    <!-- Judul -->
                    <div class="form-group">
                        <label class="form-label" for="judul">Judul Keluhan <span class="required">*</span></label>
                        <input type="text" id="judul" name="judul"
                               class="form-control <?= isset($errors['judul']) ? 'is-invalid' : '' ?>"
                               placeholder="Contoh: Jalan rusak di RT 03 RW 02"
                               value="<?= htmlspecialchars($old['judul'] ?? '') ?>"
                               maxlength="255" required>
                        <?php if (isset($errors['judul'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['judul']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Kategori -->
                    <div class="form-group">
                        <label class="form-label" for="kategori">Kategori Keluhan <span class="required">*</span></label>
                        <select id="kategori" name="kategori" class="form-control <?= isset($errors['kategori']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach (['Infrastruktur','Kebersihan','Keamanan','Pelayanan Publik','Kesehatan','Pendidikan','Sosial','Lainnya'] as $kat): ?>
                            <option value="<?= $kat ?>" <?= ($old['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['kategori'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['kategori']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Isi Laporan -->
                    <div class="form-group">
                        <label class="form-label" for="isi">Isi Laporan <span class="required">*</span></label>
                        <textarea id="isi" name="isi"
                                  class="form-control <?= isset($errors['isi']) ? 'is-invalid' : '' ?>"
                                  placeholder="Deskripsikan keluhan Anda secara detail: apa masalahnya, sejak kapan, dampaknya, dll..."
                                  rows="6" required><?= htmlspecialchars($old['isi'] ?? '') ?></textarea>
                        <div class="form-hint">Minimal 20 karakter</div>
                        <?php if (isset($errors['isi'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['isi']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Foto Bukti -->
                    <div class="form-group">
                        <label class="form-label" for="foto_bukti">Foto Bukti <span class="form-hint-inline">(opsional)</span></label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <input type="file" id="foto_bukti" name="foto_bukti"
                                   class="file-upload-input <?= isset($errors['foto_bukti']) ? 'is-invalid' : '' ?>"
                                   accept="image/jpeg,image/png,image/webp,image/gif">
                            <div class="file-upload-placeholder" id="fileUploadPlaceholder">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <p>Klik atau seret foto ke sini</p>
                                <span>JPG, PNG, WebP, GIF – Maks. 5MB</span>
                            </div>
                            <div class="file-upload-preview" id="fileUploadPreview" style="display:none">
                                <img id="previewImg" src="" alt="Preview foto">
                                <button type="button" class="file-remove-btn" id="fileRemoveBtn" aria-label="Hapus foto">✕</button>
                            </div>
                        </div>
                        <?php if (isset($errors['foto_bukti'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['foto_bukti']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Grid: Lokasi + Tanggal -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="lokasi">Lokasi Kejadian <span class="form-hint-inline">(opsional)</span></label>
                            <input type="text" id="lokasi" name="lokasi"
                                   class="form-control"
                                   placeholder="Contoh: Jl. Mawar RT 03 RW 02"
                                   value="<?= htmlspecialchars($old['lokasi'] ?? '') ?>"
                                   maxlength="255">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tanggal_kejadian">Tanggal Kejadian <span class="form-hint-inline">(opsional)</span></label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian"
                                   class="form-control <?= isset($errors['tanggal_kejadian']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($old['tanggal_kejadian'] ?? '') ?>"
                                   max="<?= date('Y-m-d') ?>">
                            <?php if (isset($errors['tanggal_kejadian'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['tanggal_kejadian']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green btn-lg w-100 btn-center" id="btn-submit-keluhan">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Kirim Keluhan
                    </button>
                </form>
                <?php endif; ?>

                <!-- Riwayat Keluhan (jika login & ada data) -->
                <?php if ($isLoggedIn && !empty($keluhanUser)): ?>
                <div class="riwayat-section">
                    <div class="riwayat-header">
                        <h3>Keluhan Terakhir Saya</h3>
                        <a href="keluhan-saya.php" class="btn-link-green">Lihat Semua →</a>
                    </div>
                    <div class="riwayat-list">
                        <?php foreach ($keluhanUser as $k): ?>
                        <a href="keluhan-saya.php?id=<?= $k['id'] ?>" class="riwayat-item">
                            <div class="riwayat-item-left">
                                <span class="riwayat-kode"><?= htmlspecialchars($k['kode_tiket']) ?></span>
                                <span class="riwayat-judul"><?= htmlspecialchars($k['judul']) ?></span>
                                <span class="riwayat-kategori"><?= htmlspecialchars($k['kategori']) ?></span>
                            </div>
                            <span class="status-badge status-badge--<?= $k['status'] ?>"><?= statusLabel($k['status']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ── KANAN: Info Kontak + Status Info ── -->
            <div class="kontak-sticky">

                <?php if ($isLoggedIn): ?>
                <!-- User Info Card -->
                <div class="card mb-6 user-welcome-card">
                    <div class="card-body">
                        <div class="user-welcome-inner">
                            <div class="user-welcome-avatar">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="user-welcome-name">Halo, <?= htmlspecialchars($user['nama']) ?>!</div>
                                <div class="user-welcome-email"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                        </div>
                        <div class="user-welcome-actions">
                            <a href="keluhan-saya.php" class="btn btn-outline-green btn-sm w-100 btn-center">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Pantau Keluhan Saya
                            </a>
                            <a href="logout-masyarakat.php" class="btn-link-logout mt-2">Keluar</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>



                <!-- Info Kontak -->
                <div class="card mb-6">
                    <div class="card-body">
                        <h3 class="mb-4 contact-section-title">Informasi Kontak</h3>
                        <div class="contact-info-list">
                            <?php
                            $contacts = [
                                ['icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>', 'label' => 'Alamat Kantor', 'val' => 'Jl. Desa Sukamaju No. 1, Kec. Cikaret, Kab. Sukabumi'],
                                ['icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 10.23a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>', 'label' => 'Telepon', 'val' => '(0266) 123-456'],
                                ['icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>', 'label' => 'Email', 'val' => 'desa.sukamaju@gmail.com'],
                                ['icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', 'label' => 'Jam Pelayanan', 'val' => 'Senin – Jumat: 08.00 – 16.00 WIB<br>Sabtu – Minggu: Tutup'],
                            ];
                            foreach ($contacts as $c): ?>
                            <div class="contact-info-row">
                                <div class="contact-info-icon-box" aria-hidden="true"><?= $c['icon'] ?></div>
                                <div>
                                    <div class="contact-info-label"><?= $c['label'] ?></div>
                                    <div class="contact-info-value"><?= $c['val'] ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <div class="alert-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="alert-note-text">
                        <strong>Perhatian:</strong> Untuk keperluan mendesak, silakan datang langsung ke kantor desa atau hubungi melalui telepon.
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
// File upload preview
const fileInput    = document.getElementById('foto_bukti');
const uploadArea   = document.getElementById('fileUploadArea');
const placeholder  = document.getElementById('fileUploadPlaceholder');
const preview      = document.getElementById('fileUploadPreview');
const previewImg   = document.getElementById('previewImg');
const removeBtn    = document.getElementById('fileRemoveBtn');

if (fileInput) {
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                placeholder.style.display = 'none';
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            fileInput.value = '';
            previewImg.src = '';
            preview.style.display = 'none';
            placeholder.style.display = 'block';
        });
    }

    // Drag & drop
    uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
    uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
