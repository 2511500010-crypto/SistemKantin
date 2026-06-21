<?php
$role = $_SESSION['role'];

if (isset($_GET['hapus']) && $role == 'admin') {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = '$id'");
    echo "<script>window.location='index.php?page=master/menu';</script>";
    exit;
}

if (isset($_POST['update_stok'])) {
    $id_menu = $_POST['id_menu'];
    $stok    = $_POST['stok'];
    $tersedia = $stok > 0 ? 1 : 0;
    mysqli_query($koneksi, "UPDATE menu SET stok = '$stok', is_available = '$tersedia' WHERE id_menu = '$id_menu'");
    echo "<script>window.location='index.php?page=master/menu';</script>";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY kategori, nm_menu");
?>

<div class="page-heading">
    <h3>Menu Makanan & Minuman</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Menu</h4>
                <?php if ($role == 'admin') : ?>
                <a href="index.php?page=master/tambah_menu" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Menu
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if (!empty($row['gambar']) && file_exists('image/menu/' . $row['gambar'])) : ?>
                                        <img src="image/menu/<?= htmlspecialchars($row['gambar']) ?>"
                                             alt="<?= htmlspecialchars($row['nm_menu']) ?>"
                                             style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                    <?php else : ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light"
                                             style="width:50px;height:50px;border-radius:8px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nm_menu']) ?></td>
                                <td class="text-capitalize"><?= $row['kategori'] ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <form method="POST" action="" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">
                                        <input type="number" name="stok" value="<?= $row['stok'] ?>"
                                               class="form-control form-control-sm" style="width:80px;" min="0">
                                        <button type="submit" name="update_stok" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($row['stok'] > 0) : ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($role == 'admin') : ?>
                                    <a href="index.php?page=master/edit_menu&id=<?= $row['id_menu'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=master/menu&hapus=<?= $row['id_menu'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus menu ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>