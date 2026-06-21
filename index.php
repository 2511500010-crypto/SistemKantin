<?php
session_start();
require_once("config/koneksi.php");

if (!isset($_SESSION['role'])) {
    echo "<meta http-equiv='refresh' content='0; url=welcome.php'>";
    exit;
}

$role = $_SESSION['role'];
$page = $_GET['page'] ?? 'dashboard';

$isMaster    = strpos($page, 'master/') === 0;
$isTransaksi = strpos($page, 'transaksi/') === 0;
$isLaporan   = strpos($page, 'laporan/') === 0;
$isPesan     = strpos($page, 'pesan/') === 0;
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
                            <img src="image/logo.png" alt="Logo" style="max-width: 130px;">
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

                    <?php if ($role == 'admin') : ?>

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
                            <li class="submenu-item <?= $page == 'master/kasir' ? 'active' : '' ?>">
                                <a href="index.php?page=master/kasir" class="submenu-link">Kasir</a>
                            </li>
                            <li class="submenu-item <?= $page == 'master/pembeli' ? 'active' : '' ?>">
                                <a href="index.php?page=master/pembeli" class="submenu-link">Daftar Pembeli</a>
                            </li>
                        </ul>
                    </li>

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

                    <?php endif; ?>

                    <?php if ($role == 'kasir') : ?>

                    <li class="sidebar-item has-sub <?= $isMaster ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-collection-fill"></i>
                            <span>Master Data</span>
                        </a>
                        <ul class="submenu <?= $isMaster ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'master/menu' ? 'active' : '' ?>">
                                <a href="index.php?page=master/menu" class="submenu-link">Stok Menu</a>
                            </li>
                            <li class="submenu-item <?= $page == 'master/meja' ? 'active' : '' ?>">
                                <a href="index.php?page=master/meja" class="submenu-link">Status Meja</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub <?= $isTransaksi ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-receipt"></i>
                            <span>Pesanan</span>
                        </a>
                        <ul class="submenu <?= $isTransaksi ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'transaksi/pesanan' ? 'active' : '' ?>">
                                <a href="index.php?page=transaksi/pesanan" class="submenu-link">Semua Pesanan</a>
                            </li>
                            <li class="submenu-item <?= $page == 'transaksi/tambah_pesanan' ? 'active' : '' ?>">
                                <a href="index.php?page=transaksi/tambah_pesanan" class="submenu-link">Input Pesanan</a>
                            </li>
                        </ul>
                    </li>

                    <?php endif; ?>

                    <?php if ($role == 'pembeli') : ?>

                    <li class="sidebar-item has-sub <?= $isPesan ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-cart-fill"></i>
                            <span>Pesan</span>
                        </a>
                        <ul class="submenu <?= $isPesan ? 'submenu-open' : 'submenu-closed' ?>">
                            <li class="submenu-item <?= $page == 'pesan/menu' ? 'active' : '' ?>">
                                <a href="index.php?page=pesan/menu" class="submenu-link">Lihat Menu</a>
                            </li>
                            <li class="submenu-item <?= $page == 'pesan/checkout' ? 'active' : '' ?>">
                                <a href="index.php?page=pesan/checkout" class="submenu-link">Pesan Online</a>
                            </li>
                            <li class="submenu-item <?= $page == 'pesan/riwayat' ? 'active' : '' ?>">
                                <a href="index.php?page=pesan/riwayat" class="submenu-link">Riwayat Pesanan</a>
                            </li>
                        </ul>
                    </li>

                    <?php endif; ?>

                    <li class="sidebar-item">
                        <a href="logout.php" class="sidebar-link">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
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
                                <?= htmlspecialchars($_SESSION['nama']) ?>
                                <span class="badge bg-primary text-capitalize ms-1"><?= $role ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="logout.php">
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
            <?php
                if ($page == "dashboard") {
                    include "page/dashboard.php";
                } elseif (!file_exists("page/$page.php")) {
                    echo "File Tidak Ditemukan";
                } else {
                    include "page/$page.php";
                }
            ?>
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