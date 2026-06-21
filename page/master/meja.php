<?php
$role = $_SESSION['role'];

if (isset($_GET['hapus']) && $role == 'admin') {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM meja WHERE id_meja = '$id'");
    echo "<script>window.location='index.php?page=master/meja';</script>";
    exit;
}

if (isset($_POST['update_status'])) {
    $id_meja = $_POST['id_meja'];
    $status  = $_POST['status'];
    mysqli_query($koneksi, "UPDATE meja SET status = '$status' WHERE id_meja = '$id_meja'");
    echo "<script>window.location='index.php?page=master/meja';</script>";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM meja ORDER BY nm_meja");
?>

<div class="page-heading">
    <h3>Meja</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Meja</h4>
                <?php if ($role == 'admin') : ?>
                <a href="index.php?page=master/tambah_meja" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Meja
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($data)) :
                        switch ($row['status']) {
                            case 'tersedia': $warna = 'success'; break;
                            case 'terisi':   $warna = 'danger'; break;
                            case 'reserved': $warna = 'warning'; break;
                            default:         $warna = 'secondary';
                        }
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card border-<?= $warna ?>">
                            <div class="card-body text-center">
                                <i class="bi bi-table fs-1 text-<?= $warna ?>"></i>
                                <h5 class="mt-2 mb-1"><?= htmlspecialchars($row['nm_meja']) ?></h5>
                                <p class="text-muted mb-2">Kapasitas: <?= $row['capacity'] ?> orang</p>
                                <span class="badge bg-<?= $warna ?> text-capitalize mb-3"><?= $row['status'] ?></span>

                                <form method="POST" action="">
                                    <input type="hidden" name="id_meja" value="<?= $row['id_meja'] ?>">
                                    <select name="status" class="form-select form-select-sm mb-2"
                                            onchange="this.form.submit()">
                                        <option value="tersedia" <?= $row['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                        <option value="terisi" <?= $row['status'] == 'terisi' ? 'selected' : '' ?>>Terisi</option>
                                        <option value="reserved" <?= $row['status'] == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>

                                <?php if ($role == 'admin') : ?>
                                <div class="mt-2">
                                    <a href="index.php?page=master/edit_meja&id=<?= $row['id_meja'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=master/meja&hapus=<?= $row['id_meja'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus meja ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
</div>