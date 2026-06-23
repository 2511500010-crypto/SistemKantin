<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user = ? AND role = 'kasir'");
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo "<script>window.location='index.php?page=master/kasir';</script>";
    exit;
}

if (isset($_POST['toggle_aktif'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status_baru']);
    
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET is_active = ? WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "ss", $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Login Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td><?= $row['last_login'] ? date('d/m/Y H:i', strtotime($row['last_login'])) : '-' ?></td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                        <input type="hidden" name="status_baru" value="<?= $row['is_active'] ? 0 : 1 ?>">
                                        <button type="submit" name="toggle_aktif"
                                                class="btn btn-sm <?= $row['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                            <?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>
                                    <a href="index.php?page=master/edit_kasir&id=<?= $row['id_user'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=master/kasir&hapus=<?= $row['id_user'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus akun kasir ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
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