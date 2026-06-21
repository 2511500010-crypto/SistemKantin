<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id' AND role = 'kasir'");
    echo "<script>window.location='index.php?page=master/kasir';</script>";
    exit;
}

if (isset($_POST['toggle_aktif'])) {
    $id = $_POST['id_user'];
    $status = $_POST['status_baru'];
    mysqli_query($koneksi, "UPDATE users SET is_active = '$status' WHERE id_user = '$id'");
    echo "<script>window.location='index.php?page=master/kasir';</script>";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'kasir' ORDER BY nama");
?>

<div class="page-heading">
    <h3>Kasir</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Kasir</h4>
                <a href="index.php?page=master/tambah_kasir" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kasir
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($data)) :
                        $warna = $row['is_active'] ? 'success' : 'secondary';
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card border-<?= $warna ?>">
                            <div class="card-body text-center">
                                <?php if (!empty($row['foto']) && file_exists('image/kasir/' . $row['foto'])) : ?>
                                    <img src="image/kasir/<?= htmlspecialchars($row['foto']) ?>"
                                         alt="<?= htmlspecialchars($row['nama']) ?>"
                                         style="width:80px;height:80px;object-fit:cover;border-radius:50%;" class="mb-2">
                                <?php else : ?>
                                    <i class="bi bi-person-badge fs-1 text-<?= $warna ?>"></i>
                                <?php endif; ?>
                                <h5 class="mt-2 mb-1"><?= htmlspecialchars($row['nama']) ?></h5>
                                <p class="text-muted mb-1">@<?= htmlspecialchars($row['username']) ?></p>
                                <p class="text-muted mb-2"><?= htmlspecialchars($row['no_hp'] ?? '-') ?></p>

                                <span class="badge bg-<?= $warna ?> mb-2">
                                    <?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>

                                <p class="text-muted small mb-3">
                                    Login terakhir:<br>
                                    <?= $row['last_login'] ? date('d/m/Y H:i', strtotime($row['last_login'])) : '-' ?>
                                </p>

                                <form method="POST" action="" class="mb-2">
                                    <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                    <input type="hidden" name="status_baru" value="<?= $row['is_active'] ? 0 : 1 ?>">
                                    <button type="submit" name="toggle_aktif"
                                            class="btn btn-sm w-100 <?= $row['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                        <?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>

                                <div>
                                    <a href="index.php?page=master/edit_kasir&id=<?= $row['id_user'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=master/kasir&hapus=<?= $row['id_user'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus akun kasir ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
</div>