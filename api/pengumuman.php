<?php
/**
 * api/pengumuman.php
 * Mengembalikan daftar pengumuman dalam format JSON
 */

require_once 'cors.php';
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

try {
    $stmt = $pdo->query(
        "SELECT id, judul, isi, tanggal FROM pengumuman ORDER BY tanggal DESC"
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = array_map(function($row) {
        return [
            'id'        => (int)$row['id'],
            'judul'     => $row['judul'],
            'kutipan'   => mb_substr(strip_tags($row['isi']), 0, 120) . '...',
            'isi'       => $row['isi'],
            'tanggal'   => $row['tanggal'],
            'tanggal_f' => (new DateTime($row['tanggal']))->format('d M Y'),
        ];
    }, $rows);

    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data pengumuman.']);
}
