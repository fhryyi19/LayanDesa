<?php
/**
 * api/kontak.php
 * Terima POST JSON lalu simpan ke tabel pesan
 */

require_once 'cors.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

require_once '../config/database.php';

// Baca JSON body
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    // Coba dari form-data
    $body = $_POST;
}

$nama   = trim($body['nama']   ?? '');
$email  = trim($body['email']  ?? '');
$subjek = trim($body['subjek'] ?? '');
$isi    = trim($body['isi']    ?? '');

$errors = [];

if (strlen($nama) < 3)   $errors[] = 'Nama minimal 3 karakter.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
if (strlen($subjek) < 5) $errors[] = 'Subjek minimal 5 karakter.';
if (strlen($isi) < 20)   $errors[] = 'Pesan minimal 20 karakter.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO pesan (nama, email, subjek, isi) VALUES (:nama, :email, :subjek, :isi)"
    );
    $stmt->execute([
        ':nama'   => $nama,
        ':email'  => $email,
        ':subjek' => $subjek,
        ':isi'    => $isi,
    ]);

    echo json_encode(['status' => 'ok', 'message' => 'Pesan berhasil dikirim!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesan.']);
}
