<?php
session_start();
include 'config/koneksi.php';

$page = $_GET['page'] ?? 'dashboard';
$allowed = [
    'dashboard',
    'master/menu', 'master/tambah_menu', 'master/edit_menu',
    'master/meja', 'master/tambah_meja', 'master/edit_meja',
    'master/user', 'master/tambah_user', 'master/edit_user',
    'transaksi/pesanan', 'transaksi/tambah_pesanan', 'transaksi/detail_pesanan',
    'laporan/laporan',
];
if (!in_array($page, $allowed)) $page = 'dashboard';

$isMaster     = strpos($page, 'master/') === 0;
$isTransaksi  = strpos($page, 'transaksi/') === 0;
$isLaporan    = strpos($page, 'laporan/') === 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kantin</title>
    <link rel="stylesheet" href="assets/compiled/css/app.css">
    <link rel="stylesheet" href="assets/compiled/css/app-dark.css">
</head>
<body>
<div id="app">

    <div id="sidebar" class="active">
        <div class="sidebar-wrapper active">

            <div class="sidebar-header position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="index.php">
                            <h4 class="text-primary fw-bold mb-0">🍽️ Sistem Kantin</h4>
                        </a>
                    </div>
                    <div class="sidebar-toggler x">
                        <a href="#" class="sidebar-hide d-xl-none d-block">
                            <i class="bi bi-x bi-middle"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="sidebar-menu">
                <ul class="menu">

                    <li class="sidebar-title">Utama</li>

                    <li class="sidebar-item <?= $page == 'dashboard' ? 'active' : '' ?>">
                        <a href="index.php?page=dashboard" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Menu</li>

                    <!-- Master Data dengan submenu accordion -->
                    <li class="sidebar-item has-sub <?= $isMaster ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-collection-fill"></i>
                            <span>Master Data</span>
                        </a>
                        <ul class="submenu <?= $isMaster ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'master/menu' ? 'active' : '' ?>">
                                <a href="index.php?page=master/menu" class="submenu-link">Menu Makanan</a>
                            </li>
                            <li class="submenu-item <?= $page == 'master/meja' ? 'active' : '' ?>">
                                <a href="index.php?page=master/meja" class="submenu-link">Meja</a>
                            </li>
                            <li class="submenu-item <?= $page == 'master/user' ? 'active' : '' ?>">
                                <a href="index.php?page=master/user" class="submenu-link">Pengguna</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Transaksi dengan submenu accordion -->
                    <li class="sidebar-item has-sub <?= $isTransaksi ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-receipt"></i>
                            <span>Transaksi</span>
                        </a>
                        <ul class="submenu <?= $isTransaksi ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'transaksi/pesanan' ? 'active' : '' ?>">
                                <a href="index.php?page=transaksi/pesanan" class="submenu-link">Pesanan</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Laporan dengan submenu accordion -->
                    <li class="sidebar-item has-sub <?= $isLaporan ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-bar-chart-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul class="submenu <?= $isLaporan ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'laporan/laporan' ? 'active' : '' ?>">
                                <a href="index.php?page=laporan/laporan" class="submenu-link">Laporan Penjualan</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    <div id="main">
        <header class="mb-3">
            <nav class="navbar navbar-expand navbar-light navbar-top">
                <div class="container-fluid">
                    <a href="#" class="burger-btn d-block">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown me-2">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?= $_SESSION['nama'] ?? 'Admin' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="auth/logout.php">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div id="main-content">
            <?php include "page/{$page}.php"; ?>
        </div>

        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>2026 &copy; Sistem Kantin</p>
                </div>
            </div>
        </footer>
    </div>

</div>

<script src="assets/static/js/initTheme.js"></script>
<script src="assets/compiled/js/app.js"></script>
<script src="assets/static/js/components/sidebar.js"></script>

</body>
</html>