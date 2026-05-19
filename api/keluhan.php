<?php
/**
 * api/keluhan.php
 * API Endpoint for Keluhan (List, Detail, Submit)
 */

require_once '../config/database.php';
require_once '../auth-masyarakat.php';

require_once 'cors.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Allow POST action to be sent in JSON body or Form Data
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? $action;
}

if (!isUserLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

if ($method === 'GET' && $action === 'list') {
    $filter = $_GET['status'] ?? '';
    $allowedFilter = ['menunggu','diterima','diproses','selesai','ditolak',''];
    if (!in_array($filter, $allowedFilter)) $filter = '';

    $sql = "SELECT id, kode_tiket, judul, kategori, status, created_at FROM keluhan WHERE user_id = ?";
    $params = [$user['id']];
    if ($filter !== '') { $sql .= " AND status = ?"; $params[] = $filter; }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $list = $stmt->fetchAll();

    // Stats
    $statsStmt = $pdo->prepare("SELECT status, COUNT(*) as cnt FROM keluhan WHERE user_id = ? GROUP BY status");
    $statsStmt->execute([$user['id']]);
    $statsRaw = $statsStmt->fetchAll();
    $stats = ['menunggu'=>0,'diterima'=>0,'diproses'=>0,'selesai'=>0,'ditolak'=>0,'total'=>0];
    foreach ($statsRaw as $s) {
        $stats[$s['status']] = (int)$s['cnt'];
        $stats['total'] += (int)$s['cnt'];
    }

    echo json_encode(['status' => 'ok', 'data' => ['list' => $list, 'stats' => $stats]]);
    exit;
}

if ($method === 'GET' && $action === 'detail') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT k.*, u.nama_lengkap FROM keluhan k JOIN users u ON k.user_id = u.id WHERE k.id = ? AND k.user_id = ?");
    $stmt->execute([$id, $user['id']]);
    $detail = $stmt->fetch();

    if ($detail) {
        // Fix image path if exists
        if ($detail['foto_bukti']) {
            // Absolute URL path or relative from root
            $detail['foto_bukti_url'] = '../' . $detail['foto_bukti'];
        }

        $tStmt = $pdo->prepare("SELECT t.*, a.username as admin_nama FROM tanggapan_keluhan t LEFT JOIN admin a ON t.admin_id = a.id WHERE t.keluhan_id = ? ORDER BY t.created_at ASC");
        $tStmt->execute([$id]);
        $timeline = $tStmt->fetchAll();

        echo json_encode(['status' => 'ok', 'data' => ['detail' => $detail, 'timeline' => $timeline]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Keluhan tidak ditemukan.']);
    }
    exit;
}

if ($method === 'POST' && $action === 'submit') {
    $judul            = trim($input['judul'] ?? '');
    $kategori         = trim($input['kategori'] ?? '');
    $isi              = trim($input['isi'] ?? '');
    $lokasi           = trim($input['lokasi'] ?? '');
    $tanggal_kejadian = trim($input['tanggal_kejadian'] ?? '');

    $allowedKategori = ['Infrastruktur','Kebersihan','Keamanan','Pelayanan Publik','Kesehatan','Pendidikan','Sosial','Lainnya'];
    $errors = [];

    if (empty($judul) || strlen($judul) < 5) $errors[] = 'Judul minimal 5 karakter.';
    if (empty($kategori) || !in_array($kategori, $allowedKategori)) $errors[] = 'Kategori tidak valid.';
    if (empty($isi) || strlen($isi) < 20) $errors[] = 'Isi laporan minimal 20 karakter.';
    
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors, 'message' => $errors[0]]);
        exit;
    }

    $fotoBuktiPath = null;
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_NO_FILE) {
        // use the same helper as web
        $uploadResult = handleFotoUpload($_FILES['foto_bukti']);
        if (isset($uploadResult['error'])) {
            echo json_encode(['status' => 'error', 'message' => $uploadResult['error']]);
            exit;
        } else {
            $fotoBuktiPath = $uploadResult['path'];
        }
    } else if (!empty($input['foto_bukti_base64'])) {
        // Optional: Support Base64 upload for mobile
        $data = $input['foto_bukti_base64'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                echo json_encode(['status' => 'error', 'message' => 'Format foto tidak valid.']);
                exit;
            }
            $data = base64_decode($data);
            if ($data === false) {
                echo json_encode(['status' => 'error', 'message' => 'Base64 decode failed.']);
                exit;
            }
            $extMap  = ['jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp', 'gif' => 'gif', 'jpg' => 'jpg'];
            $safeExt = $extMap[$type];

            $uploadDir = __DIR__ . '/../uploads/keluhan/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . $safeExt;
            file_put_contents($uploadDir . $fileName, $data);
            $fotoBuktiPath = 'uploads/keluhan/' . $fileName;
        }
    }

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

        $pdo->prepare("INSERT INTO tanggapan_keluhan (keluhan_id, jenis, status_baru, pesan) VALUES (?, 'sistem', 'menunggu', ?)")
            ->execute([$keluhanId, 'Keluhan berhasil dikirim dan menunggu verifikasi admin.']);

        echo json_encode(['status' => 'ok', 'data' => ['kode_tiket' => $kode]]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan keluhan.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
