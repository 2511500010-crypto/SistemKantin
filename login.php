<?php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi,
        "SELECT * FROM users WHERE username = '$username' AND password = '$password'"
    );

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if ($user['is_active'] == 0) {
            $error = "Akun Anda tidak aktif. Hubungi admin.";
        } else {
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['role']     = $user['role'];

            mysqli_query($koneksi, "UPDATE users SET last_login = NOW() WHERE id_user = '{$user['id_user']}'");

            header("Location: index.php?page=dashboard");
            exit;
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kantin</title>
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
                <h1 class="auth-title text-center mt-3">Masuk.</h1>
                <p class="auth-subtitle mb-5 text-center">Masuk ke akun Anda untuk melanjutkan.</p>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" name="username" class="form-control form-control-xl"
                               placeholder="Username" required>
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" class="form-control form-control-xl"
                               placeholder="Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary btn-block btn-lg shadow-lg mt-4">
                        Login
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-gray-600">
                        Belum punya akun?
                        <a href="register.php" class="font-bold">Daftar di sini</a>
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