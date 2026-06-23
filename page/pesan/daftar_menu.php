<?php
$filter_kategori = $_GET['kategori'] ?? '';
$where = $filter_kategori ? "WHERE kategori = '$filter_kategori' AND is_available = 1 AND stok > 0" : "WHERE is_available = 1 AND stok > 0";
$data = mysqli_query($koneksi, "SELECT * FROM menu $where ORDER BY kategori, nm_menu");
?>

<div class="page-heading">
    <h3>Menu Makanan & Minuman</h3>
</div>

<div class="page-content">
    <section class="section">

        <div class="d-flex gap-2 mb-4">
            <a href="index.php?page=pesan/menu" class="btn <?= !$filter_kategori ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
            <a href="index.php?page=pesan/menu&kategori=makanan" class="btn <?= $filter_kategori == 'makanan' ? 'btn-warning' : 'btn-outline-warning' ?>">🍽️ Makanan</a>
            <a href="index.php?page=pesan/menu&kategori=minuman" class="btn <?= $filter_kategori == 'minuman' ? 'btn-info' : 'btn-outline-info' ?>">🥤 Minuman</a>
            <a href="index.php?page=pesan/menu&kategori=snack" class="btn <?= $filter_kategori == 'snack' ? 'btn-success' : 'btn-outline-success' ?>">🍿 Snack</a>
        </div>

        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <?php if (!empty($row['gambar']) && file_exists('image/menu/' . $row['gambar'])): ?>
                    <img src="image/menu/<?= htmlspecialchars($row['gambar']) ?>"
                         class="card-img-top" style="height:180px;object-fit:cover;">
                    <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height:180px;">
                        <i class="bi bi-egg-fried text-muted" style="font-size:4rem;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge bg-secondary text-capitalize mb-1"><?= $row['kategori'] ?></span>
                        <h5 class="card-title"><?= htmlspecialchars($row['nm_menu']) ?></h5>
                        <?php if ($row['deskripsi']): ?>
                        <p class="card-text text-muted small"><?= htmlspecialchars($row['deskripsi']) ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-primary fs-5">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                            <span class="text-muted small">Stok: <?= $row['stok'] ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="index.php?page=pesan/checkout&id_menu=<?= $row['id_menu'] ?>"
                           class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($data) == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada menu tersedia</p>
            </div>
            <?php endif; ?>
        </div>

    </section>
</div>