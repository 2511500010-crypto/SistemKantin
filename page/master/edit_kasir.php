<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id' AND role = 'kasir'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>window.location='index.php?page=master/kasir';</script>";
    exit;
}

$kasir = mysqli_fetch_assoc($cek);
$error = '';

if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $no_hp    = $_POST['no_hp'];
    $password = $_POST['password'];

    $namaFoto = $kasir['foto'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ekstensi = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $namaFoto = 'kasir_' . time() . '.' . $ekstensi;
        $tujuan = 'image/kasir/' . $namaFoto;

        if (!is_dir('image/kasir')) {
            mkdir('image/kasir', 0777, true);
        }

        if (!empty($kasir['foto']) && file_exists('image/kasir/' . $kasir['foto'])) {
            unlink('image/kasir/' . $kasir['foto']);
        }

        move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan);
    }

    $cekUsername = mysqli_query($koneksi,
        "SELECT * FROM users WHERE username = '$username' AND id_user != '$id'"
    );

    if (mysqli_num_rows($cekUsername) > 0) {
        $error = "Username sudah digunakan oleh akun lain.";
    } else {
        if (!empty($password)) {
            $update = mysqli_query($koneksi,
                "UPDATE users SET nama = '$nama', username = '$username', no_hp = '$no_hp', password = '$password', foto = '$namaFoto'
                 WHERE id_user = '$id'"
            );
        } else {
            $update = mysqli_query($koneksi,
                "UPDATE users SET nama = '$nama', username = '$username', no_hp = '$no_hp', foto = '$namaFoto'
                 WHERE id_user = '$id'"
            );
        }

        if ($update) {
            echo "<script>window.location='index.php?page=master/kasir';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="page-heading">
    <h3>Edit Kasir</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-body">

                <?php if ($error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($kasir['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($kasir['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control"
                               value="<?= htmlspecialchars($kasir['no_hp'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-muted">(kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto <span class="text-muted">(opsional, kosongkan jika tidak ingin mengubah)</span></label>

                        <?php if (!empty($kasir['foto']) && file_exists('image/kasir/' . $kasir['foto'])) : ?>
                        <div class="mb-2">
                            <img src="image/kasir/<?= htmlspecialchars($kasir['foto']) ?>"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
                        </div>
                        <?php endif; ?>

                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php?page=master/kasir" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>