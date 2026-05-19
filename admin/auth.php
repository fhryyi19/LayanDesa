<?php
/**
 * admin/auth.php - Guard File: Proteksi Halaman Admin
 * Include file ini di SETIAP halaman admin
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Variabel global admin
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminUser = $_SESSION['admin_user'] ?? '';
