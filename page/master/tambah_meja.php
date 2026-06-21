<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $nm_meja  = $_POST['nm_meja'];
    $capacity = $_POST['capacity'];

    $insert = mysqli_query($koneksi,
        "INSERT INTO meja (nm_meja, capacity, status) VALUES ('$nm_meja', '$capacity', 'tersedia')"
    );

    if ($insert) {
        echo "<script>window.location='index.php?page=master/meja';</script>";
        exit;
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>

<div class="page-heading">
    <h3>Tambah Meja</h3>
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
                        <input type="text" name="nm_meja" class="form-control" placeholder="Contoh: Meja 8" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kapasitas (orang)</label>
                        <input type="number" name="capacity" class="form-control" min="1" value="4" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=master/meja" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>