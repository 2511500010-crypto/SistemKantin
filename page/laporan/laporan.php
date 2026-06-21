<?php
// page/laporan/laporan.php

if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-01');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

$query = "
    SELECT 
        DATE(p.tgl_pesan) as tanggal,
        COUNT(DISTINCT p.id_pesanan) as total_pesanan,
        SUM(p.total_harga) as total_pendapatan,
        SUM(CASE WHEN p.status = 'selesai' THEN 1 ELSE 0 END) as pesanan_selesai,
        SUM(CASE WHEN p.status = 'batal' THEN 1 ELSE 0 END) as pesanan_batal,
        COUNT(DISTINCT p.id_user) as pelanggan_unik
    FROM pesanan p
    WHERE DATE(p.tgl_pesan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        AND p.status IN ('selesai', 'batal')
    GROUP BY DATE(p.tgl_pesan)
    ORDER BY tanggal DESC
";
$data = mysqli_query($koneksi, $query);

$query_total = "
    SELECT 
        COUNT(*) as total_pesanan,
        SUM(total_harga) as total_pendapatan,
        COUNT(DISTINCT id_user) as pelanggan_unik
    FROM pesanan
    WHERE DATE(tgl_pesan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        AND status = 'selesai'
";
$total = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total));

$query_menu = "
    SELECT 
        mn.nm_menu,
        mn.kategori,
        SUM(dp.jumlah) as total_terjual,
        SUM(dp.subtotal) as total_pendapatan
    FROM detail_pesanan dp
    INNER JOIN menu mn ON dp.id_menu = mn.id_menu
    INNER JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    WHERE DATE(p.tgl_pesan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        AND p.status = 'selesai'
    GROUP BY dp.id_menu
    ORDER BY total_terjual DESC
    LIMIT 10
";
$menu_terlaris = mysqli_query($koneksi, $query_menu);
?>

<div class="page-heading">
    <h3>Laporan Penjualan</h3>
</div>

<div class="page-content">
    <section class="section">

        <div class="card">
            <div class="card-body">
                <form method="GET" action="">
                    <input type="hidden" name="page" value="laporan/laporan">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" class="form-control" 
                                   value="<?= $tanggal_awal ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control" 
                                   value="<?= $tanggal_akhir ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search"></i> Tampilkan
                            </button>
                            <a href="index.php?page=laporan/laporan" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Pesanan Selesai</h6>
                        <h3><?= number_format($total['total_pesanan'] ?? 0) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Pendapatan</h6>
                        <h3>Rp <?= number_format($total['total_pendapatan'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Pelanggan Unik</h6>
                        <h3><?= number_format($total['pelanggan_unik'] ?? 0) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4>Detail Penjualan Per Hari</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Pesanan</th>
                                <th>Selesai</th>
                                <th>Batal</th>
                                <th>Pelanggan Unik</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            while ($row = mysqli_fetch_assoc($data)) : 
                                $grand_total += $row['total_pendapatan'];
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= $row['total_pesanan'] ?></td>
                                <td><span class="badge bg-success"><?= $row['pesanan_selesai'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $row['pesanan_batal'] ?></span></td>
                                <td><?= $row['pelanggan_unik'] ?></td>
                                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($data) == 0) : ?>
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    <p class="text-muted">Belum ada data penjualan</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (mysqli_num_rows($data) > 0) : ?>
                        <tfoot>
                            <tr class="table-primary">
                                <th>Total</th>
                                <th colspan="4"></th>
                                <th>Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4>Menu Terlaris</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Total Terjual</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($menu_terlaris)) : 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nm_menu']) ?></strong></td>
                                <td><span class="badge bg-secondary text-capitalize"><?= $row['kategori'] ?></span></td>
                                <td><?= $row['total_terjual'] ?>x</td>
                                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($menu_terlaris) == 0) : ?>
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <p class="text-muted">Belum ada data penjualan menu</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>