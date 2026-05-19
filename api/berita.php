<?php
/**
 * api/berita.php
 * Mengembalikan daftar berita dalam format JSON
 */

require_once 'cors.php';
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$limit = min($limit, 50); // max 50

try {
    $stmt = $pdo->prepare(
        "SELECT id, judul, isi, gambar, tanggal
         FROM berita
         ORDER BY tanggal DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data
    $data = array_map(function($row) {
        return [
            'id'       => (int)$row['id'],
            'judul'    => $row['judul'],
            'kutipan'  => mb_substr(strip_tags($row['isi']), 0, 150) . '...',
            'isi'      => $row['isi'],
            'foto'     => $row['gambar'] ?? null,
            'tanggal'  => $row['tanggal'],
            'tanggal_f'=> (new DateTime($row['tanggal']))->format('d M Y'),
        ];
    }, $rows);

    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data berita.']);
}
