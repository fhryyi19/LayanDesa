<?php
/**
 * auth-masyarakat.php
 * Helper session & auth untuk masyarakat (bukan admin)
 * Disertakan di halaman yang butuh cek login user
 */

if (session_status() === PHP_SESSION_NONE) {
    // Hapus session_set_cookie_params Strict karena memblokir cross-origin dari aplikasi mobile
    $headers = getallheaders();
    $sessionId = $headers['X-Session-Id'] ?? $_SERVER['HTTP_X_SESSION_ID'] ?? null;
    if ($sessionId) {
        session_id($sessionId);
    }
    session_start();
}

/**
 * Cek apakah user sudah login
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Dapatkan data user yang sedang login
 */
function getCurrentUser(): array {
    return [
        'id'           => $_SESSION['user_id']   ?? null,
        'nama'         => $_SESSION['user_nama']  ?? '',
        'email'        => $_SESSION['user_email'] ?? '',
        'no_hp'        => $_SESSION['user_hp']    ?? '',
    ];
}

/**
 * Login user — set session
 */
function loginUser(array $user): void {
    session_regenerate_id(true); // Prevent session fixation
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_nama']  = $user['nama_lengkap'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_hp']    = $user['no_hp'];
}

/**
 * Logout user
 */
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Generate kode tiket unik
 */
function generateKodeTiket(PDO $pdo): string {
    $year = date('Y');
    do {
        $rand = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $kode = "KLH-{$year}-{$rand}";
        $exists = $pdo->prepare("SELECT id FROM keluhan WHERE kode_tiket = ?");
        $exists->execute([$kode]);
    } while ($exists->fetchColumn());
    return $kode;
}

/**
 * Label status keluhan
 */
function statusLabel(string $status): string {
    $labels = [
        'menunggu'  => 'Menunggu Verifikasi',
        'diterima'  => 'Diterima',
        'diproses'  => 'Sedang Diproses',
        'selesai'   => 'Selesai',
        'ditolak'   => 'Ditolak',
    ];
    return $labels[$status] ?? ucfirst($status);
}

/**
 * CSS class badge status
 */
function statusBadgeClass(string $status): string {
    $classes = [
        'menunggu'  => 'badge-warning',
        'diterima'  => 'badge-info',
        'diproses'  => 'badge-primary',
        'selesai'   => 'badge-success',
        'ditolak'   => 'badge-danger',
    ];
    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Icon SVG status
 */
function statusIcon(string $status): string {
    $icons = [
        'menunggu' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'diterima' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        'diproses' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>',
        'selesai'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'ditolak'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    ];
    return $icons[$status] ?? '';
}

/**
 * Format tanggal Indonesia dengan waktu
 */
function formatTanggalWaktu(string $datetime): string {
    $bulan = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
        '04' => 'Apr', '05' => 'Mei', '06' => 'Jun',
        '07' => 'Jul', '08' => 'Agu', '09' => 'Sep',
        '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
    ];
    $ts = strtotime($datetime);
    $d  = date('d', $ts);
    $m  = $bulan[date('m', $ts)];
    $y  = date('Y', $ts);
    $t  = date('H:i', $ts);
    return "{$d} {$m} {$y}, {$t} WIB";
}

/**
 * Validasi & simpan upload foto keluhan
 * Returns: ['path' => string] atau ['error' => string]
 */
function handleFotoUpload(array $file): array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Terjadi kesalahan saat upload foto.'];
    }

    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return ['error' => 'Ukuran foto maksimal 5MB.'];
    }

    // Validasi MIME type
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mimeType, $allowed)) {
        return ['error' => 'Format foto harus JPG, PNG, WebP, atau GIF.'];
    }

    // Validasi ekstensi
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $safeExt = $extMap[$mimeType] ?? 'jpg';

    // Generate nama file aman
    $uploadDir = __DIR__ . '/uploads/keluhan/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . $safeExt;
    $destPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['error' => 'Gagal menyimpan foto. Silakan coba lagi.'];
    }

    return ['path' => 'uploads/keluhan/' . $fileName];
}
