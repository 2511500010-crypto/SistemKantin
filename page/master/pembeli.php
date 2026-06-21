<?php
// page/master/pembeli.php

if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id' AND role = 'pembeli'");
    echo "<script>window.location='index.php?page=master/pembeli';</script>";
    exit;
}

if (isset($_POST['toggle_aktif'])) {
    $id = $_POST['id_user'];
    $status = $_POST['status_baru'];
    mysqli_query($koneksi, "UPDATE users SET is_active = '$status' WHERE id_user = '$id'");
    echo "<script>window.location='index.php?page=master/pembeli';</script>";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'pembeli' ORDER BY nama");
?>

<div class="page-heading">
    <h3>Daftar Pembeli</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Pembeli</h4>
                <div>
                    <span class="badge bg-primary me-2">Total: <?= mysqli_num_rows($data) ?> Pembeli</span>
                    <a href="index.php?page=master/tambah_pembeli" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Pembeli
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            while ($row = mysqli_fetch_assoc($data)) : 
                                $warna = $row['is_active'] ? 'success' : 'secondary';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                </td>
                                <td>@<?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $warna ?>">
                                        <?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                </td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                        <input type="hidden" name="status_baru" value="<?= $row['is_active'] ? 0 : 1 ?>">
                                        <button type="submit" name="toggle_aktif" 
                                                class="btn btn-sm <?= $row['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                            <?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>
                                    <a href="index.php?page=master/edit_pembeli&id=<?= $row['id_user'] ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=master/pembeli&hapus=<?= $row['id_user'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus pembeli ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($data) == 0) : ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-people fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data pembeli</p>
                                    <a href="index.php?page=master/tambah_pembeli" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Tambah Pembeli
                                    </a>
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