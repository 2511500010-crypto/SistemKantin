<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$cek = mysqli_query($koneksi, "SELECT * FROM meja WHERE id_meja = '$id'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>window.location='index.php?page=master/meja';</script>";
    exit;
}

$meja = mysqli_fetch_assoc($cek);
$error = '';

if (isset($_POST['simpan'])) {
    $nm_meja  = $_POST['nm_meja'];
    $capacity = $_POST['capacity'];
    $status   = $_POST['status'];

    $update = mysqli_query($koneksi,
        "UPDATE meja SET nm_meja = '$nm_meja', capacity = '$capacity', status = '$status'
         WHERE id_meja = '$id'"
    );

    if ($update) {
        echo "<script>window.location='index.php?page=master/meja';</script>";
        exit;
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>

<div class="page-heading">
    <h3>Edit Meja</h3>
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
                        <label class="form-label">Nama Meja</label>
                        <input type="text" name="nm_meja" class="form-control"
                               value="<?= htmlspecialchars($meja['nm_meja']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kapasitas (orang)</label>
                        <input type="number" name="capacity" class="form-control" min="1"
                               value="<?= $meja['capacity'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="tersedia" <?= $meja['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="terisi" <?= $meja['status'] == 'terisi' ? 'selected' : '' ?>>Terisi</option>
                            <option value="reserved" <?= $meja['status'] == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        </select>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php?page=master/meja" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>