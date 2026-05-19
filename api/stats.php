<?php
/**
 * api/stats.php
 * Statistik ringkasan desa
 */

require_once 'cors.php';
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

try {
    $totalBerita     = (int)$pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
    $totalPengumuman = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
    $totalPesan      = (int)$pdo->query("SELECT COUNT(*) FROM pesan")->fetchColumn();

    echo json_encode([
        'status' => 'ok',
        'data'   => [
            'penduduk'   => 3247,
            'kk'         => 847,
            'rt'         => 24,
            'rw'         => 6,
            'dusun'      => 3,
            'berita'     => $totalBerita,
            'pengumuman' => $totalPengumuman,
            'pesan'      => $totalPesan,
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil statistik.']);
}
