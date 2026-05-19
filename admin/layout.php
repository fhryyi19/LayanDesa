<?php
/**
 * admin/layout.php - Template Layout Admin
 *
 * Variabel yang harus diset sebelum include:
 * - $pageTitle   : Judul halaman
 * - $activePage  : Nama halaman aktif untuk sidebar
 */

require_once '../config/database.php';

// Hitung pesan belum dibaca untuk badge notif
$unreadCount = (int) $pdo->query("SELECT COUNT(*) FROM pesan WHERE is_read = 0")->fetchColumn();
// Hitung keluhan menunggu (aman jika tabel belum ada)
try {
    $pendingKeluhan = (int) $pdo->query("SELECT COUNT(*) FROM keluhan WHERE status = 'menunggu'")->fetchColumn();
} catch (PDOException $e) {
    $pendingKeluhan = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> – LayanDesa Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-keluhan.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>

<div class="admin-wrapper">

    <!-- ============================
         SIDEBAR
         ============================ -->
    <aside class="sidebar" id="adminSidebar" role="navigation" aria-label="Navigasi Admin">

        <!-- Header Sidebar -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="logo-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                </div>
                <div class="logo-text">
                    <span class="name">LayanDesa</span>
                    <span class="sub">Admin Panel</span>
                </div>
            </div>
        </div>

        <!-- Navigasi -->
        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>

            <a href="dashboard.php"
               class="sidebar-link <?= ($activePage === 'dashboard') ? 'active' : '' ?>"
               id="nav-dashboard">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                </span>
                Dashboard
            </a>

            <div class="nav-section-label">Konten</div>

            <a href="berita.php"
               class="sidebar-link <?= ($activePage === 'berita') ? 'active' : '' ?>"
               id="nav-berita">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                </span>
                Kelola Berita
            </a>

            <a href="pengumuman.php"
               class="sidebar-link <?= ($activePage === 'pengumuman') ? 'active' : '' ?>"
               id="nav-pengumuman">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </span>
                Kelola Pengumuman
            </a>

            <div class="nav-section-label">Interaksi</div>

            <a href="pesan.php"
               class="sidebar-link <?= ($activePage === 'pesan') ? 'active' : '' ?>"
               id="nav-pesan">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                Pesan Masuk
                <?php if ($unreadCount > 0): ?>
                <span class="badge-count"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>

            <a href="keluhan.php"
               class="sidebar-link <?= ($activePage === 'keluhan') ? 'active' : '' ?>"
               id="nav-keluhan">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                </span>
                Keluhan Masyarakat
                <?php if ($pendingKeluhan > 0): ?>
                <span class="badge-count"><?= $pendingKeluhan ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-section-label">Pengaturan</div>

            <a href="../index.php" target="_blank" class="sidebar-link" id="nav-lihat-web">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                Lihat Website
            </a>

            <a href="logout.php" class="sidebar-link sidebar-link--logout" id="nav-logout">
                <span class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </span>
                Keluar
            </a>
        </nav>

        <!-- Footer Sidebar (Info User) -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                </div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($adminName) ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>

    </aside>

    <!-- ============================
         MAIN CONTENT
         ============================ -->
    <main class="main-content" id="mainContent">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button id="sidebarToggle" class="topbar-btn" aria-label="Toggle Sidebar" style="display:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <span class="topbar-title"><?= htmlspecialchars($pageTitle ?? '') ?></span>
            </div>
            <div class="topbar-right">
                <a href="pesan.php" class="topbar-btn" aria-label="Pesan Masuk" id="topbar-pesan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <?php if ($unreadCount > 0): ?>
                    <span class="notif-dot" aria-label="<?= $unreadCount ?> pesan belum dibaca"></span>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="topbar-btn" aria-label="Logout" id="topbar-logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            </div>
        </header>

        <!-- Page Content (diisi oleh setiap halaman) -->
        <div class="page-content">
