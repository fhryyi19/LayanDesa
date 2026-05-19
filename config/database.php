<?php
/**
 * Konfigurasi Koneksi Database
 * LayanDesa - Website Pelayanan Publik Desa
 */

// Pengaturan koneksi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'layandesa');
define('DB_CHARSET', 'utf8mb4');

// Pengaturan zona waktu
date_default_timezone_set('Asia/Jakarta');

// Membuat koneksi PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Kirim JSON agar mobile app bisa parse error dengan benar
    // (bukan HTML yang akan crash di JSON.parse())
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    http_response_code(503);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal terhubung ke database. Pastikan MySQL sudah berjalan dan database "layandesa" sudah dibuat.',
    ]);
    exit;
}

// Fungsi helper untuk format tanggal Indonesia
function formatTanggal($date) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober', '11' => 'November',  '12' => 'Desember'
    ];
    $d = date('d', strtotime($date));
    $m = date('m', strtotime($date));
    $y = date('Y', strtotime($date));
    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

// Fungsi untuk memotong teks
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Fungsi untuk sanitasi input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
