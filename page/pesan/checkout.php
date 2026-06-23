<?php
if ($_SESSION['role'] != 'pembeli') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];
$error   = '';

if (isset($_POST['simpan'])) {
    $id_meja = $_POST['id_meja'];
    $catatan = $_POST['catatan'];
    $items   = $_POST['items'] ?? [];

    $valid = false;
    foreach ($items as $item) {
        if ((int)$item['jumlah'] > 0 && !empty($item['id_menu'])) {
            $valid = true;
            break;
        }
    }

    if (!$valid) {
        $error = "Pilih minimal satu menu!";
    } elseif (empty($id_meja)) {
        $error = "Silakan pilih meja!";
    } else {
        $insert = mysqli_query($koneksi,
            "INSERT INTO pesanan (id_user, id_meja, catatan, status, total_harga)
             VALUES ('$id_user', '$id_meja', '$catatan', 'pending', 0)"
        );

        if ($insert) {
            $id_pesanan = mysqli_insert_id($koneksi);
            $total = 0;

            foreach ($items as $item) {
                $id_menu = $item['id_menu'];
                $jumlah  = (int)$item['jumlah'];
                if ($jumlah <= 0 || empty($id_menu)) continue;

                $cek_menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = '$id_menu'"));
                $subtotal = $cek_menu['harga'] * $jumlah;
                $total   += $subtotal;

                mysqli_query($koneksi,
                    "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal)
                     VALUES ('$id_pesanan', '$id_menu', '$jumlah', '$subtotal')"
                );

                mysqli_query($koneksi, "UPDATE menu SET stok = stok - $jumlah WHERE id_menu = '$id_menu'");
            }

            mysqli_query($koneksi, "UPDATE pesanan SET total_harga = '$total' WHERE id_pesanan = '$id_pesanan'");
            mysqli_query($koneksi, "UPDATE meja SET status = 'terisi' WHERE id_meja = '$id_meja'");

            echo "<script>window.location='index.php?page=pesan/riwayat';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan pesanan: " . mysqli_error($koneksi);
        }
    }
}

$id_menu_default = $_GET['id_menu'] ?? '';
$mejas = mysqli_query($koneksi, "SELECT * FROM meja WHERE status = 'tersedia' ORDER BY nm_meja");
$menus = mysqli_query($koneksi, "SELECT * FROM menu WHERE is_available = 1 AND stok > 0 ORDER BY kategori, nm_menu");
?>

<div class="page-heading">
    <h3>Pesan Online</h3>
</div>

<div class="page-content">
    <section class="section">

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pilih Menu</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
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
                                                <?php while ($row = mysqli_fetch_assoc($menus)): ?>
                                                <option value="<?= $row['id_menu'] ?>"
                                                        data-harga="<?= $row['harga'] ?>"
                                                        data-stok="<?= $row['stok'] ?>"
                                                        <?= $row['id_menu'] == $id_menu_default ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($row['nm_menu']) ?> (Stok: <?= $row['stok'] ?>)
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </td>
                                        <td><span id="harga_0">-</span></td>
                                        <td><span id="stok_0">-</span></td>
                                        <td>
                                            <input type="number" name="items[0][jumlah]" value="1" min="1"
                                                   class="form-control form-control-sm" style="width:80px;"
                                                   onchange="hitungTotal()">
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
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Info Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Meja</label>
                            <?php if (mysqli_num_rows($mejas) > 0): ?>
                            <select name="id_meja" class="form-select" required>
                                <option value="">-- Pilih Meja --</option>
                                <?php while ($meja = mysqli_fetch_assoc($mejas)): ?>
                                <option value="<?= $meja['id_meja'] ?>">
                                    <?= htmlspecialchars($meja['nm_meja']) ?> (<?= $meja['capacity'] ?> orang)
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <?php else: ?>
                            <div class="alert alert-warning">Tidak ada meja tersedia.</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3"
                                      placeholder="Catatan untuk dapur (opsional)"></textarea>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-primary fs-5" id="total-harga">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary w-100">
                            <i class="bi bi-bag-check"></i> Pesan Sekarang
                        </button>
                        <a href="index.php?page=pesan/daftar_menu" class="btn btn-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> Kembali ke Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </form>

    </section>
</div>

<script>
let rowIndex = 1;
const menuOptions = `<?php
    mysqli_data_seek($menus, 0);
    while ($row = mysqli_fetch_assoc($menus)):
        echo "<option value='{$row['id_menu']}' data-harga='{$row['harga']}' data-stok='{$row['stok']}'>"
           . htmlspecialchars($row['nm_menu']) . " (Stok: {$row['stok']})</option>";
    endwhile;
?>`;

function tambahBaris() {
    const tbody = document.getElementById('bodyMenu');
    const row   = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][id_menu]" class="form-select" onchange="updateHarga(this, ${rowIndex})">
                <option value="">-- Pilih Menu --</option>${menuOptions}
            </select>
        </td>
        <td><span id="harga_${rowIndex}">-</span></td>
        <td><span id="stok_${rowIndex}">-</span></td>
        <td><input type="number" name="items[${rowIndex}][jumlah]" value="1" min="1"
                   class="form-control form-control-sm" style="width:80px;" onchange="hitungTotal()"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">
            <i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    rowIndex++;
}

function hapusBaris(btn) {
    if (document.getElementById('bodyMenu').children.length > 1) {
        btn.closest('tr').remove();
        hitungTotal();
    } else {
        alert('Minimal harus ada 1 menu!');
    }
}

function updateHarga(select, index) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('harga_' + index).textContent = opt.dataset.harga
        ? 'Rp ' + parseInt(opt.dataset.harga).toLocaleString('id-ID') : '-';
    document.getElementById('stok_' + index).textContent = opt.dataset.stok || '-';
    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('#bodyMenu tr').forEach(function(tr) {
        const sel = tr.querySelector('select');
        const inp = tr.querySelector('input[type="number"]');
        if (!sel || !inp) return;
        const harga  = parseInt(sel.options[sel.selectedIndex]?.dataset?.harga) || 0;
        const jumlah = parseInt(inp.value) || 0;
        total += harga * jumlah;
    });
    document.getElementById('total-harga').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
    const sel = document.querySelector('#bodyMenu select');
    if (sel && sel.value) updateHarga(sel, 0);
});
</script>