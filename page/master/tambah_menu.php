<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $nm_menu   = $_POST['nm_menu'];
    $kategori  = $_POST['kategori'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $namaGambar = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ekstensi = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $namaGambar = 'menu_' . time() . '.' . $ekstensi;
        $tujuan = 'image/menu/' . $namaGambar;

        if (!is_dir('image/menu')) {
            mkdir('image/menu', 0777, true);
        }

        move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
    }

    $insert = mysqli_query($koneksi,
        "INSERT INTO menu (nm_menu, kategori, harga, stok, deskripsi, gambar)
         VALUES ('$nm_menu', '$kategori', '$harga', '$stok', '$deskripsi', '$namaGambar')"
    );

    if ($insert) {
        echo "<script>window.location='index.php?page=master/menu';</script>";
        exit;
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>

<div class="page-heading">
    <h3>Tambah Menu</h3>
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
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nm_menu" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar Menu <span class="text-muted">(opsional)</span></label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=master/menu" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>