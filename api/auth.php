<?php
/**
 * api/auth.php
 * API Endpoint for Authentication (Login, Register, Me, Logout)
 */

require_once '../config/database.php';
require_once '../auth-masyarakat.php';

require_once 'cors.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Allow POST action to be sent in JSON body
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? $action;
}

if ($action === 'me') {
    if (isUserLoggedIn()) {
        $user = getCurrentUser();
        echo json_encode(['status' => 'ok', 'data' => ['user' => $user, 'token' => session_id()]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    }
    exit;
}

if ($action === 'logout') {
    logoutUser();
    echo json_encode(['status' => 'ok', 'data' => null]);
    exit;
}

if ($method === 'POST' && $action === 'login') {
    $email    = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email dan password harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            echo json_encode(['status' => 'ok', 'data' => ['user' => getCurrentUser(), 'token' => session_id()]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email atau password salah.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
    exit;
}

if ($method === 'POST' && $action === 'register') {
    $nama     = trim($input['nama'] ?? '');
    $email    = strtolower(trim($input['email'] ?? ''));
    $no_hp    = trim($input['no_hp'] ?? '');
    $password = trim($input['password'] ?? '');

    $errors = [];
    if (empty($nama) || strlen($nama) < 3) $errors[] = 'Nama minimal 3 karakter.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (empty($no_hp) || !preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) $errors[] = 'Nomor HP tidak valid.';
    if (empty($password) || strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password minimal 8 karakter dan mengandung angka.';
    }

    if (empty($errors)) {
        try {
            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $chk->execute([$email]);
            if ($chk->fetchColumn()) {
                $errors[] = 'Email ini sudah terdaftar.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem.';
        }
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors, 'message' => $errors[0]]);
        exit;
    }

    try {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, no_hp, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $email, $no_hp, $hash]);

        echo json_encode(['status' => 'ok', 'data' => ['message' => 'Registrasi berhasil']]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat akun. Silakan coba lagi.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
