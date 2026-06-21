<?php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: index.php?page=dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kantin</title>
    <link rel="stylesheet" href="assets/compiled/css/app.css">
    <link rel="stylesheet" href="assets/compiled/css/app-dark.css">
</head>
<body>
<div id="auth">
    <div class="row h-100 align-items-center justify-content-center">
        <div class="col-lg-6 col-12 text-center">

            <div class="auth-logo mb-4">
                <img src="image/logo.png" alt="Logo Kantin" style="max-width: 280px;">
            </div>

            <p class="auth-subtitle mb-5 fs-4">
                Pesan makanan dan minuman favoritmu dengan mudah, langsung dari mejamu.
            </p>

            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-primary btn-lg shadow-lg px-5">
                    Login
                </a>
                <a href="register.php" class="btn btn-outline-primary btn-lg px-5">
                    Daftar
                </a>
            </div>

        </div>
    </div>
</div>

<script src="assets/static/js/initTheme.js"></script>
<script src="assets/compiled/js/app.js"></script>
</body>
</html>