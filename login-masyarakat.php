<?php
/**
 * login-masyarakat.php
 * Halaman Login Masyarakat - Sistem Keluhan LayanDesa
 */

require_once 'config/database.php';
require_once 'auth-masyarakat.php';

// Redirect kalau sudah login
if (isUserLoggedIn()) {
    header('Location: kontak.php');
    exit;
}

$pageTitle  = 'Login Masyarakat';
$activePage = 'kontak';
$basePath   = '';
$metaDesc   = 'Login untuk mengakses sistem pengaduan masyarakat Desa Sukamaju.';

$errors = [];
$old    = [];
$alert  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    // CSRF sederhana: cek referrer
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $old      = compact('email');

    if (empty($email)) {
        $errors['email'] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password tidak boleh kosong.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([strtolower($email)]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                loginUser($user);
                // Redirect ke halaman sebelumnya atau kontak
                $redirect = $_SESSION['redirect_after_login'] ?? 'kontak.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $errors['general'] = 'Email atau password salah. Silakan periksa kembali.';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Login Masyarakat</h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="index.php">Beranda</a>
                <span class="sep">›</span>
                <a href="kontak.php">Kontak</a>
                <span class="sep">›</span>
                <span>Login</span>
            </nav>
        </div>
    </div>
</div>

<!-- Auth Section -->
<section class="content-section auth-section">
    <div class="container">
        <div class="auth-grid">

            <!-- Auth Card -->
            <div class="auth-card-wrapper">
                <div class="auth-card">
                    <div class="auth-card-header">
                        <div class="auth-icon-circle">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h2>Masuk ke Akun Anda</h2>
                        <p>Login untuk mengakses sistem pengaduan masyarakat</p>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div><?= htmlspecialchars($errors['general']) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success" role="alert">
                        <div class="alert-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div><strong>Registrasi berhasil!</strong> Silakan login dengan akun Anda.</div>
                    </div>
                    <?php endif; ?>

                    <form id="formLogin" method="POST" action="login-masyarakat.php" novalidate>
                        <input type="hidden" name="action" value="login">

                        <div class="form-group">
                            <label class="form-label" for="login_email">Email <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <input type="email" id="login_email" name="email"
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
                            <label class="form-label" for="login_password">Password <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input type="password" id="login_password" name="password"
                                       class="form-control with-icon <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Masukkan password"
                                       autocomplete="current-password" required>
                                <button type="button" class="toggle-password" aria-label="Tampilkan/sembunyikan password" data-target="login_password">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-green btn-lg w-100 btn-center" id="btn-login">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Masuk
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>Belum punya akun?</span>
                    </div>

                    <a href="register.php" class="btn btn-outline-green btn-lg w-100 btn-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Daftar Akun Baru
                    </a>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="auth-info-panel">
                <div class="auth-info-card">
                    <div class="auth-info-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
                            <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                        </svg>
                    </div>
                    <h3>Sistem Pengaduan Masyarakat</h3>
                    <p>Platform resmi untuk menyampaikan keluhan dan aspirasi kepada Pemerintah Desa Sukamaju.</p>
                </div>

                <div class="auth-features">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon auth-feature-icon--green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <strong>Terdata Resmi</strong>
                            <p>Setiap keluhan mendapatkan nomor tiket resmi</p>
                        </div>
                    </div>
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon auth-feature-icon--blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <strong>Tracking Real-time</strong>
                            <p>Pantau progres penanganan keluhan Anda</p>
                        </div>
                    </div>
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon auth-feature-icon--orange">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <strong>Tanggapan Langsung</strong>
                            <p>Dapatkan respon dari petugas desa</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
