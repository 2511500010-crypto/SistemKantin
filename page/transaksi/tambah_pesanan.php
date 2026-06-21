<?php
// page/transaksi/tambah_pesanan.php

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'kasir') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

$users = mysqli_query($koneksi, "SELECT id_user, nama, username FROM users WHERE role = 'pembeli' AND is_active = 1");
$meja = mysqli_query($koneksi, "SELECT id_meja, nm_meja FROM meja WHERE status = 'tersedia'");
$menu = mysqli_query($koneksi, "SELECT id_menu, nm_menu, harga, stok FROM menu WHERE stok > 0 AND is_available = 1");

if (isset($_POST['simpan'])) {
    $id_user = $_POST['id_user'];
    $id_meja = $_POST['id_meja'];
    $catatan = $_POST['catatan'];
    $items = $_POST['items'] ?? array();

    if (empty($items)) {
        $error = "Silakan pilih minimal satu menu!";
    } else {
        $insert_pesanan = mysqli_query($koneksi,
            "INSERT INTO pesanan (id_user, id_meja, catatan, status, total_harga) 
             VALUES ('$id_user', '$id_meja', '$catatan', 'pending', 0)"
        );

        if ($insert_pesanan) {
            $id_pesanan = mysqli_insert_id($koneksi);
            $total = 0;

            foreach ($items as $item) {
                $id_menu = $item['id_menu'];
                $jumlah = $item['jumlah'];
                
                $cek_menu = mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = '$id_menu'");
                $data_menu = mysqli_fetch_assoc($cek_menu);
                $subtotal = $data_menu['harga'] * $jumlah;
                $total += $subtotal;

                mysqli_query($koneksi,
                    "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) 
                     VALUES ('$id_pesanan', '$id_menu', '$jumlah', '$subtotal')"
                );

                mysqli_query($koneksi, "UPDATE menu SET stok = stok - $jumlah WHERE id_menu = '$id_menu'");
            }

            mysqli_query($koneksi, "UPDATE pesanan SET total_harga = '$total' WHERE id_pesanan = '$id_pesanan'");
            mysqli_query($koneksi, "UPDATE meja SET status = 'terisi' WHERE id_meja = '$id_meja'");

            echo "<script>window.location='index.php?page=transaksi/pesanan';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="page-heading">
    <h3>Tambah Pesanan</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-body">

                <?php if ($error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="formPesanan">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pembeli</label>
                                <select name="id_user" class="form-select" required>
                                    <option value="">-- Pilih Pembeli --</option>
                                    <?php while ($row = mysqli_fetch_assoc($users)) : ?>
                                    <option value="<?= $row['id_user'] ?>">
                                        <?= htmlspecialchars($row['nama']) ?> (@<?= htmlspecialchars($row['username']) ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Meja</label>
                                <select name="id_meja" class="form-select" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php while ($row = mysqli_fetch_assoc($meja)) : ?>
                                    <option value="<?= $row['id_meja'] ?>">
                                        <?= htmlspecialchars($row['nm_meja']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pesanan (opsional)"></textarea>
                    </div>

                    <hr>
                    <h5>Pilih Menu</h5>

                    <div class="table-responsive">
                        <table class="table" id="tabelMenu">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bodyMenu">
                                <tr>
                                    <td>
                                        <select name="items[0][id_menu]" class="form-select" onchange="updateHarga(this, 0)">
                                            <option value="">-- Pilih Menu --</option>
                                            <?php while ($row = mysqli_fetch_assoc($menu)) : ?>
                                            <option value="<?= $row['id_menu'] ?>" data-harga="<?= $row['harga'] ?>" data-stok="<?= $row['stok'] ?>">
                                                <?= htmlspecialchars($row['nm_menu']) ?> (Stok: <?= $row['stok'] ?>)
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </td>
                                    <td><span id="harga_0">0</span></td>
                                    <td><span id="stok_0">0</span></td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]" class="form-control" 
                                               style="width:80px;" min="1" value="1" onchange="hitungSubtotal(0)">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">
                                            <i class="bi bi-plus-circle"></i> Tambah Menu
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Pesanan</button>
                    <a href="index.php?page=transaksi/pesanan" class="btn btn-secondary">Batal</a>

                </form>

            </div>
        </div>
    </section>
</div>

<script>
let rowIndex = 1;

function tambahBaris() {
    const tbody = document.getElementById('bodyMenu');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][id_menu]" class="form-select" onchange="updateHarga(this, ${rowIndex})">
                <option value="">-- Pilih Menu --</option>
                <?php 
                mysqli_data_seek($menu, 0);
                while ($row = mysqli_fetch_assoc($menu)) : ?>
                <option value="<?= $row['id_menu'] ?>" data-harga="<?= $row['harga'] ?>" data-stok="<?= $row['stok'] ?>">
                    <?= htmlspecialchars($row['nm_menu']) ?> (Stok: <?= $row['stok'] ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </td>
        <td><span id="harga_${rowIndex}">0</span></td>
        <td><span id="stok_${rowIndex}">0</span></td>
        <td>
            <input type="number" name="items[${rowIndex}][jumlah]" class="form-control" 
                   style="width:80px;" min="1" value="1" onchange="hitungSubtotal(${rowIndex})">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    rowIndex++;
}

function hapusBaris(btn) {
    const row = btn.closest('tr');
    if (document.getElementById('bodyMenu').children.length > 1) {
        row.remove();
    } else {
        alert('Minimal harus ada 1 menu!');
    }
}

function updateHarga(select, index) {
    const option = select.options[select.selectedIndex];
    const harga = option.dataset.harga || 0;
    const stok = option.dataset.stok || 0;
    document.getElementById(`harga_${index}`).textContent = harga;
    document.getElementById(`stok_${index}`).textContent = stok;
}

function hitungSubtotal(index) {
    // Fungsi ini bisa dikembangkan untuk perhitungan otomatis
}
</script>