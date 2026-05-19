<?php
/**
 * Header & Navbar - LayanDesa
 * Digunakan di semua halaman public
 */

$pageTitle = $pageTitle ?? 'LayanDesa';
$activePage = $activePage ?? '';
$fullTitle = $pageTitle !== 'LayanDesa' ? $pageTitle . ' - LayanDesa' : 'LayanDesa | Website Pelayanan Publik Desa';
$metaDesc = $metaDesc ?? 'LayanDesa – Portal resmi pelayanan publik Desa Sukamaju. Informasi berita, pengumuman, layanan administrasi, dan pengaduan masyarakat.';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta name="theme-color" content="#1a6b3c">
    <title><?= htmlspecialchars($fullTitle) ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= $basePath ?? '' ?>assets/img/favicon.svg" type="image/svg+xml">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Merriweather:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $basePath ?? '' ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $basePath ?? '' ?>assets/css/org-photo.css">
    <link rel="stylesheet" href="<?= $basePath ?? '' ?>assets/css/keluhan.css">
</head>

<body>

    <!-- ============================
     NAVBAR
     ============================ -->
    <nav class="navbar" id="mainNavbar" aria-label="Navigasi Utama">
        <div class="navbar-inner">

            <!-- Brand Logo -->
            <a href="<?= $basePath ?? '' ?>index.php" class="navbar-brand" aria-label="LayanDesa - Halaman Utama">
                <div class="brand-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z" />
                        <polyline points="9 21 9 12 15 12 15 21" />
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-name">LayanDesa</span>
                    <span class="brand-tagline">Desa Sukamaju</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <ul class="navbar-nav" id="navMenu" role="list">
                <li>
                    <a href="<?= $basePath ?? '' ?>index.php"
                        class="nav-link <?= ($activePage === 'home') ? 'active' : '' ?>" id="nav-home">Beranda</a>
                </li>
                <li>
                    <a href="<?= $basePath ?? '' ?>profil.php"
                        class="nav-link <?= ($activePage === 'profil') ? 'active' : '' ?>" id="nav-profil">Profil
                        Desa</a>
                </li>
                <li>
                    <a href="<?= $basePath ?? '' ?>berita.php"
                        class="nav-link <?= ($activePage === 'berita') ? 'active' : '' ?>" id="nav-berita">Berita</a>
                </li>
                <li>
                    <a href="<?= $basePath ?? '' ?>pengumuman.php"
                        class="nav-link <?= ($activePage === 'pengumuman') ? 'active' : '' ?>"
                        id="nav-pengumuman">Pengumuman</a>
                </li>
                <li>
                    <a href="<?= $basePath ?? '' ?>layanan.php"
                        class="nav-link <?= ($activePage === 'layanan') ? 'active' : '' ?>" id="nav-layanan">Layanan</a>
                </li>
                <li>
                    <a href="<?= $basePath ?? '' ?>kontak.php"
                        class="nav-link <?= ($activePage === 'kontak') ? 'active' : '' ?>" id="nav-kontak">Kontak</a>
                </li>

            </ul>

            <!-- Hamburger Button (Mobile) -->
            <button class="hamburger" id="hamburger" aria-label="Toggle Menu" aria-expanded="false"
                aria-controls="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </nav>
    <?php /* End Navbar */ ?>