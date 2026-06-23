<?php
if ($_SESSION['role'] != 'pembeli') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];

$data = mysqli_query($koneksi, "
    SELECT p.id_pesanan, p.tgl_pesan, p.status, p.total_harga, p.catatan,
           m.nm_meja,
           (SELECT COUNT(*) FROM detail_pesanan WHERE id_pesanan = p.id_pesanan) as jumlah_item
    FROM pesanan p
    INNER JOIN meja m ON p.id_meja = m.id_meja
    WHERE p.id_user = '$id_user'
    ORDER BY p.tgl_pesan DESC
");
?>

<div class="page-heading">
    <h3>Riwayat Pesanan</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Pesanan Saya</h4>
                <a href="index.php?page=pesan/checkout" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Pesan Lagi
                </a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($data) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Meja</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)):
                                switch($row['status']) {
                                    case 'pending': $bc = 'warning'; $row_class = 'table-warning'; break;
                                    case 'proses':  $bc = 'info';    $row_class = 'table-info'; break;
                                    case 'selesai': $bc = 'success'; $row_class = 'table-success'; break;
                                    default:        $bc = 'danger';  $row_class = 'table-danger';
                                }
                            ?>
                            <tr class="<?= $row_class ?>">
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nm_meja']) ?></td>
                                <td><?= $row['jumlah_item'] ?> item</td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="badge bg-<?= $bc ?> text-capitalize"><?= $row['status'] ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tgl_pesan'])) ?></td>
                                <td>
                                    <a href="index.php?page=transaksi/detail_pesanan&id=<?= $row['id_pesanan'] ?>"
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Belum ada pesanan</p>
                    <a href="index.php?page=pesan/checkout" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Pesan Sekarang
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>