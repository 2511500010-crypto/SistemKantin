<?php
$role = $_SESSION['role'];

if (isset($_POST['update_status'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $status = $_POST['status'];
    mysqli_query($koneksi, "UPDATE pesanan SET status = '$status' WHERE id_pesanan = '$id_pesanan'");
    echo "<script>window.location='index.php?page=transaksi/pesanan';</script>";
    exit;
}

if (isset($_GET['hapus']) && $role == 'admin') {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan = '$id'");
    echo "<script>window.location='index.php?page=transaksi/pesanan';</script>";
    exit;
}

$query = "
    SELECT 
        p.*,
        COALESCE(u.nama, p.nama_pelanggan, 'Walk-in') as nama_pelanggan,
        COALESCE(u.username, '-') as username,
        m.nm_meja,
        (SELECT COUNT(*) FROM detail_pesanan WHERE id_pesanan = p.id_pesanan) as jumlah_item
    FROM pesanan p
    LEFT JOIN users u ON p.id_user = u.id_user
    INNER JOIN meja m ON p.id_meja = m.id_meja
    ORDER BY p.created_at DESC
";

if ($role == 'pembeli') {
    $id_user = $_SESSION['id_user'];
    $query = "
        SELECT 
            p.*,
            COALESCE(u.nama, p.nama_pelanggan, 'Walk-in') as nama_pelanggan,
            COALESCE(u.username, '-') as username,
            m.nm_meja,
            (SELECT COUNT(*) FROM detail_pesanan WHERE id_pesanan = p.id_pesanan) as jumlah_item
        FROM pesanan p
        LEFT JOIN users u ON p.id_user = u.id_user
        INNER JOIN meja m ON p.id_meja = m.id_meja
        WHERE p.id_user = '$id_user'
        ORDER BY p.created_at DESC
    ";
}

$data = mysqli_query($koneksi, $query);
?>

<div class="page-heading">
    <h3>Pesanan</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Pesanan</h4>
                <?php if ($role == 'kasir' || $role == 'admin'): ?>
                <a href="index.php?page=transaksi/tambah_pesanan" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Pesanan
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Meja</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            while ($row = mysqli_fetch_assoc($data)):
                                switch($row['status']) {
                                    case 'pending': $bc = 'warning'; $row_class = 'table-warning'; break;
                                    case 'proses':  $bc = 'info';    $row_class = 'table-info'; break;
                                    case 'selesai': $bc = 'success'; $row_class = 'table-success'; break;
                                    default:        $bc = 'danger';  $row_class = 'table-danger';
                                }
                            ?>
                            <tr class="<?= $row_class ?>">
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_pelanggan']) ?></strong>
                                    <?php if ($row['username'] != '-'): ?>
                                    <div class="text-muted small">@<?= htmlspecialchars($row['username']) ?></div>
                                    <?php else: ?>
                                    <div class="text-muted small"><i class="bi bi-person-walking"></i> Walk-in</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nm_meja']) ?></td>
                                <td><?= $row['jumlah_item'] ?> item</td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($role == 'admin' || $role == 'kasir'): ?>
                                    <form method="POST" action="" class="d-flex gap-1">
                                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                                        <select name="status" class="form-select form-select-sm"
                                                style="width:120px;" onchange="this.form.submit()">
                                            <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="proses"  <?= $row['status'] == 'proses'  ? 'selected' : '' ?>>Proses</option>
                                            <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="batal"   <?= $row['status'] == 'batal'   ? 'selected' : '' ?>>Batal</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                    <?php else: ?>
                                    <span class="badge bg-<?= $bc ?> text-capitalize"><?= $row['status'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="index.php?page=transaksi/detail_pesanan&id=<?= $row['id_pesanan'] ?>"
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($role == 'admin'): ?>
                                    <a href="index.php?page=transaksi/pesanan&hapus=<?= $row['id_pesanan'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($data) == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada pesanan</p>
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