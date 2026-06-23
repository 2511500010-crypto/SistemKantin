<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id' AND role = 'kasir'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>window.location='index.php?page=master/kasir';</script>";
    exit;
}

$error = '';

if (isset($_POST['update'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    
    // Cek username tidak bentrok dengan user lain
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? AND id_user != ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah digunakan, silakan pilih username lain.";
    }
    
    // Cek email tidak bentrok dengan user lain
    if (!empty($email)) {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ? AND id_user != ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $id);
        mysqli_stmt_execute($stmt);
        $resultEmail = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($resultEmail) > 0) {
            $error = "Email sudah digunakan, silakan pilih email lain.";
        }
    }
    
    if (empty($error)) {
        if (!empty($password)) {
            // Update dengan password baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi, 
                "UPDATE users SET nama = ?, username = ?, email = ?, password = ? WHERE id_user = ?"
            );
            mysqli_stmt_bind_param($stmt, "sssss", $nama, $username, $email, $hashed_password, $id);
        } else {
            // Update tanpa password
            $stmt = mysqli_prepare($koneksi, 
                "UPDATE users SET nama = ?, username = ?, email = ? WHERE id_user = ?"
            );
            mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $email, $id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>window.location='index.php?page=master/kasir';</script>";
            exit;
        } else {
            $error = "Gagal mengupdate: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
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
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                    <a href="index.php?page=master/kasir" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>