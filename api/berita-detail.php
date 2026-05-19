<?php
/**
 * api/berita-detail.php?id=N
 * Mengembalikan detail satu berita
 */

require_once 'cors.php';
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, judul, isi, gambar, tanggal FROM berita WHERE id = :id LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Berita tidak ditemukan.']);
        exit;
    }

    $data = [
        'id'        => (int)$row['id'],
        'judul'     => $row['judul'],
        'isi'       => $row['isi'],
        'foto'      => $row['gambar'] ?? null,
        'tanggal'   => $row['tanggal'],
        'tanggal_f' => (new DateTime($row['tanggal']))->format('d M Y'),
    ];

    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data.']);
}
