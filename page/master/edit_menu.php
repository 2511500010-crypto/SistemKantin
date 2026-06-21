<?php
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$cek = mysqli_query($koneksi, "SELECT * FROM menu WHERE id_menu = '$id'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>window.location='index.php?page=master/menu';</script>";
    exit;
}

$menu = mysqli_fetch_assoc($cek);
$error = '';

if (isset($_POST['simpan'])) {
    $nm_menu   = $_POST['nm_menu'];
    $kategori  = $_POST['kategori'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $namaGambar = $menu['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ekstensi = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $namaGambar = 'menu_' . time() . '.' . $ekstensi;
        $tujuan = 'image/menu/' . $namaGambar;

        if (!is_dir('image/menu')) {
            mkdir('image/menu', 0777, true);
        }

        if (!empty($menu['gambar']) && file_exists('image/menu/' . $menu['gambar'])) {
            unlink('image/menu/' . $menu['gambar']);
        }

        move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
    }

    $update = mysqli_query($koneksi,
        "UPDATE menu SET
            nm_menu = '$nm_menu',
            kategori = '$kategori',
            harga = '$harga',
            stok = '$stok',
            deskripsi = '$deskripsi',
            gambar = '$namaGambar'
         WHERE id_menu = '$id'"
    );

    if ($update) {
        echo "<script>window.location='index.php?page=master/menu';</script>";
        exit;
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>

<div class="page-heading">
    <h3>Edit Menu</h3>
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
                        <input type="text" name="nm_menu" class="form-control"
                               value="<?= htmlspecialchars($menu['nm_menu']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="makanan" <?= $menu['kategori'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                            <option value="minuman" <?= $menu['kategori'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                            <option value="snack" <?= $menu['kategori'] == 'snack' ? 'selected' : '' ?>>Snack</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" min="0"
                               value="<?= $menu['harga'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" min="0"
                               value="<?= $menu['stok'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar Menu <span class="text-muted">(opsional, kosongkan jika tidak ingin mengubah)</span></label>

                        <?php if (!empty($menu['gambar']) && file_exists('image/menu/' . $menu['gambar'])) : ?>
                        <div class="mb-2">
                            <img src="image/menu/<?= htmlspecialchars($menu['gambar']) ?>"
                                 style="width:100px;height:100px;object-fit:cover;border-radius:8px;">
                        </div>
                        <?php endif; ?>

                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php?page=master/menu" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>