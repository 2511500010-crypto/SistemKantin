<?php
// page/master/edit_pembeli.php

if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id' AND role = 'pembeli'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>window.location='index.php?page=master/pembeli';</script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($cek);
$error = '';

if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $no_hp    = $_POST['no_hp'];
    $password = $_POST['password'];

    $cekUsername = mysqli_query($koneksi,
        "SELECT * FROM users WHERE username = '$username' AND id_user != '$id'"
    );

    if (mysqli_num_rows($cekUsername) > 0) {
        $error = "Username sudah digunakan oleh akun lain.";
    } else {
        if (!empty($password)) {
            $update = mysqli_query($koneksi,
                "UPDATE users SET nama = '$nama', username = '$username', email = '$email', no_hp = '$no_hp', password = '$password'
                 WHERE id_user = '$id'"
            );
        } else {
            $update = mysqli_query($koneksi,
                "UPDATE users SET nama = '$nama', username = '$username', email = '$email', no_hp = '$no_hp'
                 WHERE id_user = '$id'"
            );
        }

        if ($update) {
            echo "<script>window.location='index.php?page=master/pembeli';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="page-heading">
    <h3>Edit Pembeli</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-body">

                <?php if ($error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($pembeli['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($pembeli['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($pembeli['email'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control"
                               value="<?= htmlspecialchars($pembeli['no_hp'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-muted">(kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php?page=master/pembeli" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>