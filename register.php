<?php
/**
 * register.php
 * Halaman Registrasi Masyarakat - Sistem Keluhan LayanDesa
 */

require_once 'config/database.php';
require_once 'auth-masyarakat.php';

if (isUserLoggedIn()) {
    header('Location: kontak.php');
    exit;
}

$pageTitle  = 'Daftar Akun';
$activePage = 'kontak';
$basePath   = '';
$metaDesc   = 'Buat akun untuk mengakses sistem pengaduan masyarakat Desa Sukamaju.';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']      ?? '');
    $email    = strtolower(trim($_POST['email']    ?? ''));
    $no_hp    = trim($_POST['no_hp']     ?? '');
    $password = trim($_POST['password']  ?? '');
    $konfirm  = trim($_POST['konfirmasi'] ?? '');

    $old = compact('nama', 'email', 'no_hp');

    // Validasi
    if (empty($nama))             $errors['nama']    = 'Nama lengkap tidak boleh kosong.';
    elseif (strlen($nama) < 3)    $errors['nama']    = 'Nama minimal 3 karakter.';
    elseif (strlen($nama) > 150)  $errors['nama']    = 'Nama maksimal 150 karakter.';

    if (empty($email))            $errors['email']   = 'Email tidak boleh kosong.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Format email tidak valid.';

    if (empty($no_hp))            $errors['no_hp']   = 'Nomor HP tidak boleh kosong.';
    elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) $errors['no_hp'] = 'Nomor HP tidak valid (8–20 digit).';

    if (empty($password))         $errors['password'] = 'Password tidak boleh kosong.';
    elseif (strlen($password) < 8) $errors['password'] = 'Password minimal 8 karakter.';
    elseif (!preg_match('/[0-9]/', $password)) $errors['password'] = 'Password harus mengandung minimal 1 angka.';

    if (empty($konfirm))          $errors['konfirmasi'] = 'Konfirmasi password tidak boleh kosong.';
    elseif ($password !== $konfirm) $errors['konfirmasi'] = 'Konfirmasi password tidak cocok.';

    // Cek email duplikat
    if (empty($errors['email'])) {
        try {
            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $chk->execute([$email]);
            if ($chk->fetchColumn()) {
                $errors['email'] = 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Terjadi kesalahan sistem.';
        }
    }

    if (empty($errors)) {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, no_hp, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $no_hp, $hash]);

            header('Location: login-masyarakat.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $errors['general'] = 'Gagal membuat akun. Silakan coba lagi.';
        }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Daftar Akun Baru</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <a href="kontak.php">Kontak</a>
                <span class="sep">›</span>
                <span>Daftar</span>
            </nav>
        </div>
    </div>
</div>

<!-- Register Section -->
<section class="content-section auth-section">
    <div class="container">
        <div class="auth-grid">

            <!-- Register Card -->
            <div class="auth-card-wrapper">
                <div class="auth-card">
                    <div class="auth-card-header">
                        <div class="auth-icon-circle">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                        </div>
                        <h2>Buat Akun Masyarakat</h2>
                        <p>Daftarkan diri untuk mengakses sistem pengaduan online</p>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div><?= htmlspecialchars($errors['general']) ?></div>
                    </div>
                    <?php endif; ?>

                    <form id="formRegister" method="POST" action="register.php" novalidate>

                        <div class="form-group">
                            <label class="form-label" for="reg_nama">Nama Lengkap <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <input type="text" id="reg_nama" name="nama"
                                       class="form-control with-icon <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                                       placeholder="Nama lengkap sesuai KTP"
                                       value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
                                       maxlength="150" autocomplete="name" required>
                            </div>
                            <?php if (isset($errors['nama'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['nama']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg_email">Email <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <input type="email" id="reg_email" name="email"
                                       class="form-control with-icon <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       placeholder="email@contoh.com"
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                       autocomplete="email" required>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg_hp">Nomor HP <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 10.23a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <input type="tel" id="reg_hp" name="no_hp"
                                       class="form-control with-icon <?= isset($errors['no_hp']) ? 'is-invalid' : '' ?>"
                                       placeholder="08xxxxxxxxxx"
                                       value="<?= htmlspecialchars($old['no_hp'] ?? '') ?>"
                                       autocomplete="tel" required>
                            </div>
                            <?php if (isset($errors['no_hp'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['no_hp']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg_password">Password <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input type="password" id="reg_password" name="password"
                                       class="form-control with-icon <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Min. 8 karakter + angka"
                                       autocomplete="new-password" required>
                                <button type="button" class="toggle-password" aria-label="Tampilkan password" data-target="reg_password">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <div class="form-hint">Minimal 8 karakter dan mengandung angka</div>
                            <?php if (isset($errors['password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                            <!-- Password strength indicator -->
                            <div class="password-strength" id="passwordStrength" style="display:none">
                                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                                <span class="strength-text" id="strengthText"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg_konfirmasi">Konfirmasi Password <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input type="password" id="reg_konfirmasi" name="konfirmasi"
                                       class="form-control with-icon <?= isset($errors['konfirmasi']) ? 'is-invalid' : '' ?>"
                                       placeholder="Ulangi password Anda"
                                       autocomplete="new-password" required>
                                <button type="button" class="toggle-password" aria-label="Tampilkan konfirmasi password" data-target="reg_konfirmasi">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <?php if (isset($errors['konfirmasi'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['konfirmasi']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-check-group">
                            <input type="checkbox" id="setuju" name="setuju" class="form-check-input" required>
                            <label for="setuju" class="form-check-label">
                                Saya menyetujui bahwa data yang saya masukkan adalah benar dan dapat dipertanggungjawabkan.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-green btn-lg w-100 btn-center" id="btn-register">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                            Buat Akun
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>Sudah punya akun?</span>
                    </div>

                    <a href="login-masyarakat.php" class="btn btn-outline-green btn-lg w-100 btn-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Masuk ke Akun
                    </a>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="auth-info-panel">
                <div class="auth-info-card">
                    <div class="auth-info-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3>Aman & Terpercaya</h3>
                    <p>Data Anda dilindungi dan hanya digunakan untuk keperluan pelayanan masyarakat desa.</p>
                </div>

                <div class="register-steps">
                    <h4>Cara Kerja Sistem</h4>
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <strong>Daftar & Login</strong>
                            <p>Buat akun dengan email aktif Anda</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <strong>Kirim Keluhan</strong>
                            <p>Isi formulir keluhan dengan detail lengkap</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <strong>Tracking & Tanggapan</strong>
                            <p>Pantau status dan tanggapan dari petugas desa</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
