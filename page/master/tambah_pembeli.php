<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'];

    $cekUsername = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($cekUsername) > 0) {
        $error = "Username sudah digunakan, silakan pilih username lain.";
    } else {
        $insert = mysqli_query($koneksi,
            "INSERT INTO users (username, email, password, nama, role) 
             VALUES ('$username', '$email', '$password', '$nama', 'pembeli')"
        );

        if ($insert) {
            echo "<script>window.location='index.php?page=master/pembeli';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="page-heading">
    <h3>Tambah Pembeli</h3>
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
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=master/pembeli" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>