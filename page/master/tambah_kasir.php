<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $no_hp    = $_POST['no_hp'];
    $password = $_POST['password'];

    $namaFoto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ekstensi = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $namaFoto = 'kasir_' . time() . '.' . $ekstensi;
        $tujuan = 'image/kasir/' . $namaFoto;

        if (!is_dir('image/kasir')) {
            mkdir('image/kasir', 0777, true);
        }

        move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan);
    }

    $cekUsername = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($cekUsername) > 0) {
        $error = "Username sudah digunakan, silakan pilih username lain.";
    } else {
        $insert = mysqli_query($koneksi,
            "INSERT INTO users (username, password, nama, no_hp, foto, role)
             VALUES ('$username', '$password', '$nama', '$no_hp', '$namaFoto', 'kasir')"
        );

        if ($insert) {
            echo "<script>window.location='index.php?page=master/kasir';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="page-heading">
    <h3>Tambah Kasir</h3>
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
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto <span class="text-muted">(opsional)</span></label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=master/kasir" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>