<?php
// page/dashboard.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include dirname(__DIR__) . '/config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo "<script>window.location='../login.php';</script>";
    exit();
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$id_user = $_SESSION['id_user'];

$query_stats = "
    SELECT 
        (SELECT COUNT(*) FROM pesanan) as total_pesanan,
        (SELECT COUNT(*) FROM menu) as total_menu,
        (SELECT COUNT(*) FROM meja) as total_meja,
        (SELECT COUNT(*) FROM users WHERE role = 'pembeli') as total_pembeli,
        (SELECT COUNT(*) FROM pesanan WHERE status = 'pending') as pesanan_pending,
        (SELECT COUNT(*) FROM pesanan WHERE status = 'proses') as pesanan_proses,
        (SELECT COUNT(*) FROM pesanan WHERE status = 'selesai') as pesanan_selesai,
        (SELECT COUNT(*) FROM pesanan WHERE status = 'batal') as pesanan_batal,
        (SELECT COALESCE(SUM(total_harga), 0) FROM pesanan WHERE status = 'selesai') as total_pendapatan
";

$result_stats = mysqli_query($koneksi, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

$query_pesanan = "
    SELECT 
        p.id_pesanan,
        p.tgl_pesan,
        p.status,
        p.total_harga,
        p.catatan,
        u.nama as nama_pelanggan,
        u.username,
        m.nm_meja,
        (SELECT COUNT(*) FROM detail_pesanan WHERE id_pesanan = p.id_pesanan) as jumlah_item
    FROM pesanan p
    INNER JOIN users u ON p.id_user = u.id_user
    INNER JOIN meja m ON p.id_meja = m.id_meja
    ORDER BY p.created_at DESC 
    LIMIT 10
";
$pesanan_terbaru = mysqli_query($koneksi, $query_pesanan);

$query_menu_terlaris = "
    SELECT 
        mn.nm_menu,
        mn.kategori,
        SUM(dp.jumlah) as total_terjual,
        SUM(dp.subtotal) as total_pendapatan
    FROM detail_pesanan dp
    INNER JOIN menu mn ON dp.id_menu = mn.id_menu
    INNER JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    WHERE p.status IN ('proses', 'selesai')
    GROUP BY dp.id_menu
    ORDER BY total_terjual DESC
    LIMIT 5
";
$menu_terlaris = mysqli_query($koneksi, $query_menu_terlaris);

$query_meja = "
    SELECT 
        status,
        COUNT(*) as jumlah
    FROM meja
    GROUP BY status
";
$status_meja = mysqli_query($koneksi, $query_meja);
$meja_tersedia = 0;
$meja_terisi = 0;
$meja_reserved = 0;

while ($row = mysqli_fetch_assoc($status_meja)) {
    if ($row['status'] == 'tersedia') $meja_tersedia = $row['jumlah'];
    if ($row['status'] == 'terisi') $meja_terisi = $row['jumlah'];
    if ($row['status'] == 'reserved') $meja_reserved = $row['jumlah'];
}

$query_pendapatan = "
    SELECT 
        DATE(tgl_pesan) as tanggal,
        COUNT(*) as total_pesanan,
        SUM(total_harga) as pendapatan
    FROM pesanan
    WHERE status = 'selesai'
        AND tgl_pesan >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(tgl_pesan)
    ORDER BY tanggal ASC
";
$pendapatan_harian = mysqli_query($koneksi, $query_pendapatan);

$labels = array();
$values = array();
while ($row = mysqli_fetch_assoc($pendapatan_harian)) {
    $labels[] = date('d/m', strtotime($row['tanggal']));
    $values[] = $row['pendapatan'];
}

$total_pesanan = $stats['total_pesanan'] ?? 0;
$total_menu = $stats['total_menu'] ?? 0;
$total_meja = $stats['total_meja'] ?? 0;
$total_pembeli = $stats['total_pembeli'] ?? 0;
$total_pendapatan = $stats['total_pendapatan'] ?? 0;
$pesanan_pending = $stats['pesanan_pending'] ?? 0;
$pesanan_proses = $stats['pesanan_proses'] ?? 0;
$pesanan_selesai = $stats['pesanan_selesai'] ?? 0;
$pesanan_batal = $stats['pesanan_batal'] ?? 0;
?>

<div class="page-heading">
    <h3>Dashboard</h3>
</div>

<div class="page-content">
    <section class="section">

        <!-- ========================================== -->
        <!-- STATISTIK - SAMA RATA 4 KOLOM -->
        <!-- ========================================== -->
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body px-4 py-4 text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                            <i class="bi bi-receipt fs-2 text-primary"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($total_pesanan) ?></h3>
                        <h6 class="text-muted mb-2">Total Pesanan</h6>
                        <div>
                            <span class="badge bg-warning bg-opacity-10 text-warning">Pending: <?= $pesanan_pending ?></span>
                            <span class="badge bg-info bg-opacity-10 text-info">Proses: <?= $pesanan_proses ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body px-4 py-4 text-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                            <i class="bi bi-cash-stack fs-2 text-success"></i>
                        </div>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success">Selesai: <?= $pesanan_selesai ?></span>
                            <span class="badge bg-danger bg-opacity-10 text-danger">Batal: <?= $pesanan_batal ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body px-4 py-4 text-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                            <i class="bi bi-egg-fried fs-2 text-warning"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($total_menu) ?></h3>
                        <h6 class="text-muted mb-2">Total Menu</h6>
                        <div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body px-4 py-4 text-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                            <i class="bi bi-table fs-2 text-info"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($total_meja) ?></h3>
                        <h6 class="text-muted mb-2">Total Meja</h6>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success">Tersedia: <?= $meja_tersedia ?></span>
                            <span class="badge bg-danger bg-opacity-10 text-danger">Terisi: <?= $meja_terisi ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PESANAN TERBARU & MENU TERLARIS -->
        <!-- ========================================== -->
        <div class="row">
            <div class="col-xl-8 col-lg-7 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Pesanan Terbaru</h5>
                        <?php if ($role == 'admin' || $role == 'kasir'): ?>
                        <a href="#" class="btn btn-primary btn-sm">Lihat Semua</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (mysqli_num_rows($pesanan_terbaru) > 0): ?>
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Pelanggan</th>
                                        <th>Meja</th>
                                        <th>Item</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = mysqli_fetch_assoc($pesanan_terbaru)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['nama_pelanggan']) ?></strong>
                                            <div class="text-muted small">@<?= htmlspecialchars($row['username']) ?></div>
                                        </td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($row['nm_meja']) ?></span></td>
                                        <td><?= $row['jumlah_item'] ?> item</td>
                                        <td class="fw-bold">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch($row['status']) {
                                                case 'pending': $badge_class = 'warning'; break;
                                                case 'proses': $badge_class = 'info'; break;
                                                case 'selesai': $badge_class = 'success'; break;
                                                case 'batal': $badge_class = 'danger'; break;
                                            }
                                            ?>
                                            <span class="badge bg-<?= $badge_class ?> bg-opacity-10 text-<?= $badge_class ?> text-capitalize"><?= $row['status'] ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada pesanan</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-fire me-2 text-danger"></i>Menu Terlaris</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        mysqli_data_seek($menu_terlaris, 0);
                        if (mysqli_num_rows($menu_terlaris) > 0): 
                        ?>
                        <div class="list-group list-group-flush">
                            <?php $rank = 1; while ($row = mysqli_fetch_assoc($menu_terlaris)): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="fw-bold"><?= $rank++ ?>. <?= htmlspecialchars($row['nm_menu']) ?></span>
                                    <br>
                                    <small class="text-muted text-capitalize"><?= $row['kategori'] ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary"><?= $row['total_terjual'] ?>x</span>
                                    <br>
                                    <small class="text-success fw-bold">Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></small>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bar-chart fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data penjualan</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-table me-2 text-info"></i>Status Meja</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-success bg-opacity-10">
                                    <h2 class="fw-bold text-success mb-0"><?= $meja_tersedia ?></h2>
                                    <small class="text-muted">Tersedia</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-danger bg-opacity-10">
                                    <h2 class="fw-bold text-danger mb-0"><?= $meja_terisi ?></h2>
                                    <small class="text-muted">Terisi</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-warning bg-opacity-10">
                                    <h2 class="fw-bold text-warning mb-0"><?= $meja_reserved ?></h2>
                                    <small class="text-muted">Reserved</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- GRAFIK PENDAPATAN -->
        <!-- ========================================== -->
        <?php if (count($labels) > 0): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Pendapatan 7 Hari Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(67, 97, 238, 0.25)');
            gradient.addColorStop(1, 'rgba(67, 97, 238, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: <?= json_encode($values) ?>,
                        borderColor: '#4361ee',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4361ee',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        });
        </script>
        <?php endif; ?>

    </section>
</div>