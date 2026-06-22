<?php
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'kasir') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$error = '';

$users = mysqli_query($koneksi, "SELECT id_user, nama, username FROM users WHERE role = 'pembeli' AND is_active = 1");
$meja  = mysqli_query($koneksi, "SELECT id_meja, nm_meja FROM meja WHERE status = 'tersedia'");
$menu  = mysqli_query($koneksi, "SELECT id_menu, nm_menu, harga, stok FROM menu WHERE stok > 0 AND is_available = 1");

if (isset($_POST['simpan'])) {
    $tipe_pembeli   = $_POST['tipe_pembeli'];
    $id_user        = $tipe_pembeli == 'akun' ? $_POST['id_user'] : null;
    $nama_pelanggan = $tipe_pembeli == 'manual' ? $_POST['nama_pelanggan'] : null;
    $id_meja        = $_POST['id_meja'];
    $catatan        = $_POST['catatan'];
    $items          = $_POST['items'] ?? [];

    if (empty($items)) {
        $error = "Silakan pilih minimal satu menu!";
    } elseif ($tipe_pembeli == 'akun' && empty($id_user)) {
        $error = "Silakan pilih pembeli!";
    } elseif ($tipe_pembeli == 'manual' && empty($nama_pelanggan)) {
        $error = "Silakan isi nama pembeli!";
    } else {
        $id_user_sql        = $id_user ? "'$id_user'" : "NULL";
        $nama_pelanggan_sql = $nama_pelanggan ? "'".mysqli_real_escape_string($koneksi, $nama_pelanggan)."'" : "NULL";

        $insert_pesanan = mysqli_query($koneksi,
            "INSERT INTO pesanan (id_user, nama_pelanggan, id_meja, catatan, status, total_harga)
             VALUES ($id_user_sql, $nama_pelanggan_sql, '$id_meja', '$catatan', 'pending', 0)"
        );

        if ($insert_pesanan) {
            $id_pesanan = mysqli_insert_id($koneksi);
            $total = 0;

            foreach ($items as $item) {
                $id_menu = $item['id_menu'];
                $jumlah  = (int)$item['jumlah'];
                if ($jumlah <= 0 || empty($id_menu)) continue;

                $cek_menu  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = '$id_menu'"));
                $subtotal  = $cek_menu['harga'] * $jumlah;
                $total    += $subtotal;

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

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="formPesanan">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipe Pembeli</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_pembeli"
                                               id="tipe_akun" value="akun" checked onchange="toggleTipePembeli()">
                                        <label class="form-check-label" for="tipe_akun">Punya Akun</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_pembeli"
                                               id="tipe_manual" value="manual" onchange="toggleTipePembeli()">
                                        <label class="form-check-label" for="tipe_manual">Input Manual</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="div_akun">
                                <label class="form-label">Pilih Pembeli</label>
                                <select name="id_user" class="form-select">
                                    <option value="">-- Pilih Pembeli --</option>
                                    <?php while ($row = mysqli_fetch_assoc($users)): ?>
                                    <option value="<?= $row['id_user'] ?>">
                                        <?= htmlspecialchars($row['nama']) ?> (@<?= htmlspecialchars($row['username']) ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3" id="div_manual" style="display:none;">
                                <label class="form-label">Nama Pembeli</label>
                                <input type="text" name="nama_pelanggan" class="form-control"
                                       placeholder="Masukkan nama pembeli">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meja</label>
                                <select name="id_meja" class="form-select" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php while ($row = mysqli_fetch_assoc($meja)): ?>
                                    <option value="<?= $row['id_meja'] ?>">
                                        <?= htmlspecialchars($row['nm_meja']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"
                                          placeholder="Catatan pesanan (opsional)"></textarea>
                            </div>
                        </div>
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
                                            <?php mysqli_data_seek($menu, 0); while ($row = mysqli_fetch_assoc($menu)): ?>
                                            <option value="<?= $row['id_menu'] ?>" data-harga="<?= $row['harga'] ?>" data-stok="<?= $row['stok'] ?>">
                                                <?= htmlspecialchars($row['nm_menu']) ?> (Stok: <?= $row['stok'] ?>)
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </td>
                                    <td><span id="harga_0">-</span></td>
                                    <td><span id="stok_0">-</span></td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]" class="form-control"
                                               style="width:80px;" min="1" value="1">
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
const menuOptions = `<?php
    mysqli_data_seek($menu, 0);
    while ($row = mysqli_fetch_assoc($menu)):
        echo "<option value='{$row['id_menu']}' data-harga='{$row['harga']}' data-stok='{$row['stok']}'>"
           . htmlspecialchars($row['nm_menu']) . " (Stok: {$row['stok']})</option>";
    endwhile;
?>`;

function toggleTipePembeli() {
    const tipe = document.querySelector('input[name="tipe_pembeli"]:checked').value;
    document.getElementById('div_akun').style.display   = tipe == 'akun'   ? 'block' : 'none';
    document.getElementById('div_manual').style.display = tipe == 'manual' ? 'block' : 'none';
}

function tambahBaris() {
    const tbody = document.getElementById('bodyMenu');
    const row   = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][id_menu]" class="form-select" onchange="updateHarga(this, ${rowIndex})">
                <option value="">-- Pilih Menu --</option>
                ${menuOptions}
            </select>
        </td>
        <td><span id="harga_${rowIndex}">-</span></td>
        <td><span id="stok_${rowIndex}">-</span></td>
        <td>
            <input type="number" name="items[${rowIndex}][jumlah]" class="form-control"
                   style="width:80px;" min="1" value="1">
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
    if (document.getElementById('bodyMenu').children.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('Minimal harus ada 1 menu!');
    }
}

function updateHarga(select, index) {
    const option = select.options[select.selectedIndex];
    document.getElementById(`harga_${index}`).textContent = option.dataset.harga
        ? 'Rp ' + parseInt(option.dataset.harga).toLocaleString('id-ID') : '-';
    document.getElementById(`stok_${index}`).textContent = option.dataset.stok || '-';
}
</script>