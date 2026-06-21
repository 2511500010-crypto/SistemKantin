<?php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $no_hp    = $_POST['no_hp'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password !== $konfirmasi) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        $cekUsername = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

        if (mysqli_num_rows($cekUsername) > 0) {
            $error = "Username sudah digunakan, silakan pilih username lain.";
        } else {
            $insert = mysqli_query($koneksi,
                "INSERT INTO users (username, email, password, nama, no_hp, role)
                 VALUES ('$username', '$email', '$password', '$nama', '$no_hp', 'pembeli')"
            );

            if ($insert) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Kantin</title>
    <link rel="stylesheet" href="assets/compiled/css/app.css">
    <link rel="stylesheet" href="assets/compiled/css/app-dark.css">
</head>
<body>
<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12 h-100 d-flex align-items-center justify-content-center">
            <div id="auth-left" style="width:100%; max-width:380px;">
                <div class="auth-logo text-center">
                    <a href="welcome.php"><img src="image/logo.png" alt="Logo Kantin" style="max-width: 180px;"></a>
                </div>
                <h1 class="auth-title text-center mt-3">Daftar.</h1>
                <p class="auth-subtitle mb-4 text-center">Buat akun untuk mulai memesan.</p>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                    <a href="login.php" class="font-bold">Login sekarang</a>
                </div>
                <?php else: ?>

                <form method="POST" action="">
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="text" name="nama" class="form-control form-control-xl"
                               placeholder="Nama Lengkap" required>
                        <div class="form-control-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="text" name="username" class="form-control form-control-xl"
                               placeholder="Username" required>
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="email" name="email" class="form-control form-control-xl"
                               placeholder="Email">
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="text" name="no_hp" class="form-control form-control-xl"
                               placeholder="No HP">
                        <div class="form-control-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="password" name="password" class="form-control form-control-xl"
                               placeholder="Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="password" name="konfirmasi" class="form-control form-control-xl"
                               placeholder="Konfirmasi Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary btn-block btn-lg shadow-lg mt-4">
                        Daftar
                    </button>
                </form>

                <?php endif; ?>

                <div class="text-center mt-4">
                    <p class="text-gray-600">
                        Sudah punya akun?
                        <a href="login.php" class="font-bold">Login di sini</a>
                    </p>
                    <p>
                        <a href="welcome.php" class="font-bold">&larr; Kembali ke Beranda</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right" style="background: linear-gradient(135deg, #c0392b 0%, #f39c12 100%);">
            </div>
        </div>
    </div>
</div>

<script src="assets/static/js/initTheme.js"></script>
<script src="assets/compiled/js/app.js"></script>
</body>
</html>