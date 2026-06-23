<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek username dengan prepared statement
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah digunakan, silakan pilih username lain.";
    } else {
        // Cek email tidak boleh sama
        if (!empty($email)) {
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $resultEmail = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($resultEmail) > 0) {
                $error = "Email sudah digunakan, silakan pilih email lain.";
            }
        }
        
        if (empty($error)) {
            // Insert dengan kolom yang sesuai database
            $stmt = mysqli_prepare($koneksi, 
                "INSERT INTO users (username, email, password, nama, role) VALUES (?, ?, ?, ?, 'kasir')"
            );
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $nama);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>window.location='index.php?page=master/kasir';</script>";
                exit;
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
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
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
                    <a href="index.php?page=master/kasir" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>