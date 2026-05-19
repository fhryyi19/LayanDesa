<?php
/**
 * generate_password.php
 * -----------------------------------------------
 * HAPUS FILE INI setelah digunakan!
 * -----------------------------------------------
 * Gunakan file ini untuk generate hash password
 * yang aman (bcrypt) untuk admin baru.
 *
 * Akses: http://localhost/layandesa/generate_password.php
 */

$password  = 'admin123'; // Ganti dengan password yang diinginkan
$hashed    = password_hash($password, PASSWORD_DEFAULT);
$isValid   = password_verify($password, $hashed);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Password Hash</title>
    <style>
        body { font-family: monospace; max-width: 700px; margin: 50px auto; padding: 20px; background: #f4f7f5; }
        .box { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        h2 { color: #1a6b3c; }
        .hash { background: #f0f7f3; padding: 16px; border-radius: 8px; border-left: 4px solid #1a6b3c; word-break: break-all; font-size: .9rem; margin: 12px 0; }
        .warn { background: #fff3cd; padding: 12px 16px; border-radius: 8px; border-left: 4px solid #ffc107; font-size:.875rem; margin-top:16px; }
        .ok { color: #38a169; font-weight: bold; }
        label { font-weight: bold; display: block; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔐 Generate Password Hash (bcrypt)</h2>
    <p><label>Password Asli:</label> <code><?= htmlspecialchars($password) ?></code></p>
    <p><label>Hash (masukkan ke database):</label></p>
    <div class="hash"><?= htmlspecialchars($hashed) ?></div>
    <p>Verifikasi: <span class="ok"><?= $isValid ? '✅ Password cocok!' : '❌ Error!' ?></span></p>

    <div class="warn">
        ⚠️ <strong>PENTING:</strong> Segera hapus file <code>generate_password.php</code> ini setelah digunakan!
        Jangan biarkan file ini bisa diakses secara publik.
    </div>

    <p style="margin-top:16px; font-size:.85rem; color:#6b8a6c;">
        Salin hash di atas dan masukkan ke kolom <code>password</code> di tabel <code>admin</code> pada database.
    </p>
</div>
</body>
</html>
