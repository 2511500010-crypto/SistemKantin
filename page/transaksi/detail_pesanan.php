<?php
// page/transaksi/detail_pesanan.php

$id = $_GET['id'] ?? 0;

$query = "
    SELECT 
        p.*,
        u.nama as nama_pelanggan,
        u.username,
        u.email,
        u.no_hp,
        m.nm_meja
    FROM pesanan p
    INNER JOIN users u ON p.id_user = u.id_user
    INNER JOIN meja m ON p.id_meja = m.id_meja
    WHERE p.id_pesanan = '$id'
";
$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

if (!$pesanan) {
    echo "<script>window.location='index.php?page=transaksi/pesanan';</script>";
    exit;
}

$detail = mysqli_query($koneksi, "
    SELECT dp.*, mn.nm_menu, mn.kategori
    FROM detail_pesanan dp
    INNER JOIN menu mn ON dp.id_menu = mn.id_menu
    WHERE dp.id_pesanan = '$id'
");
?>

<div class="page-heading">
    <h3>Detail Pesanan</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Pesanan #<?= $id ?></h4>
                <a href="index.php?page=transaksi/pesanan" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Pelanggan</strong></td>
                                <td>: <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Username</strong></td>
                                <td>: @<?= htmlspecialchars($pesanan['username']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: <?= htmlspecialchars($pesanan['email'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>No HP</strong></td>
                                <td>: <?= htmlspecialchars($pesanan['no_hp'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Meja</strong></td>
                                <td>: <?= htmlspecialchars($pesanan['nm_meja']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: <?= date('d/m/Y H:i', strtotime($pesanan['tgl_pesan'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php
                                    $badge_class = '';
                                    switch($pesanan['status']) {
                                        case 'pending': $badge_class = 'warning'; break;
                                        case 'proses': $badge_class = 'info'; break;
                                        case 'selesai': $badge_class = 'success'; break;
                                        case 'batal': $badge_class = 'danger'; break;
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badge_class ?> text-capitalize"><?= $pesanan['status'] ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($pesanan['catatan']) : ?>
                <div class="alert alert-info">
                    <strong>Catatan:</strong> <?= htmlspecialchars($pesanan['catatan']) ?>
                </div>
                <?php endif; ?>

                <h5>Detail Menu</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $total = 0;
                            while ($row = mysqli_fetch_assoc($detail)) : 
                                $total += $row['subtotal'];
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nm_menu']) ?></strong></td>
                                <td><span class="badge bg-secondary text-capitalize"><?= $row['kategori'] ?></span></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td>Rp <?= number_format($row['subtotal'] / $row['jumlah'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="5" class="text-end">Total</th>
                                <th>Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>