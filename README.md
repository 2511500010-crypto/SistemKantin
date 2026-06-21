<ul>
    <li>
        <strong>Sistem Manajemen Kantin</strong>
        <ul>
            <li>Sistem Manajemen Kantin adalah aplikasi berbasis web yang dirancang untuk mengelola operasional kantin secara efisien.</li>
            <li>Sistem ini mencakup manajemen menu, pemesanan, meja, laporan penjualan, dan manajemen pengguna.</li>
            <li>Aplikasi ini dibangun menggunakan PHP dan MySQL dengan struktur database yang terencana dan siap dikembangkan.</li>
        </ul>
    </li>

    <li>
        <strong>Fitur Utama</strong>
        <ul>
            <li>
                <strong>Manajemen Pengguna</strong>
                <ul>
                    <li>Memiliki 3 peran pengguna yaitu Admin, Kasir, dan Pembeli.</li>
                    <li>Setiap peran memiliki hak akses yang berbeda sesuai dengan fungsinya masing-masing.</li>
                    <li>Admin memiliki akses penuh ke semua fitur.</li>
                    <li>Kasir dapat mengelola menu, meja, dan transaksi pesanan.</li>
                    <li>Pembeli dapat melihat menu dan melakukan pemesanan online.</li>
                </ul>
            </li>
            <li>
                <strong>Manajemen Meja</strong>
                <ul>
                    <li>Setiap meja memiliki status yaitu tersedia, terisi, dan reserved.</li>
                    <li>Admin dapat menambah, mengedit, dan menghapus data meja.</li>
                    <li>Kasir dapat melihat dan mengupdate status meja secara real-time.</li>
                </ul>
            </li>
            <li>
                <strong>Manajemen Menu</strong>
                <ul>
                    <li>Menu dibagi menjadi 4 kategori yaitu Makanan, Minuman, Snack, dan Dessert.</li>
                    <li>Setiap menu memiliki stok yang akan berkurang otomatis saat ada pesanan.</li>
                    <li>Jika pesanan dibatalkan, stok akan kembali otomatis.</li>
                    <li>Admin dapat menambah, mengedit, dan menghapus menu.</li>
                </ul>
            </li>
            <li>
                <strong>Manajemen Pesanan</strong>
                <ul>
                    <li>Pesanan memiliki 4 status yaitu pending, proses, selesai, dan batal.</li>
                    <li>Total harga dihitung secara otomatis oleh sistem.</li>
                    <li>Admin dan Kasir dapat mengubah status pesanan.</li>
                    <li>Pembeli dapat melihat riwayat pesanan mereka sendiri.</li>
                </ul>
            </li>
            <li>
                <strong>Laporan Penjualan</strong>
                <ul>
                    <li>Menyediakan laporan penjualan lengkap dengan filter periode tanggal.</li>
                    <li>Menampilkan total pesanan, total pendapatan, dan pelanggan unik.</li>
                    <li>Menampilkan menu terlaris berdasarkan jumlah penjualan.</li>
                    <li>Hanya dapat diakses oleh Admin.</li>
                </ul>
            </li>
            <li>
                <strong>Dashboard</strong>
                <ul>
                    <li>Menampilkan ringkasan statistik seperti total pesanan, pendapatan, menu, dan meja.</li>
                    <li>Menampilkan pesanan terbaru dan menu terlaris.</li>
                    <li>Menampilkan grafik pendapatan 7 hari terakhir.</li>
                    <li>Tampilan dashboard berbeda sesuai dengan role pengguna.</li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        <strong>Teknologi yang Digunakan</strong>
        <ul>
            <li>Backend: PHP 7.4+</li>
            <li>Database: MySQL 5.7 / 8.0</li>
            <li>Frontend: Bootstrap 5, Font Awesome, Chart.js</li>
            <li>Fitur Database: Triggers, Stored Procedures, Views</li>
        </ul>
    </li>

    <li>
            <li>
                <strong>Import Database</strong>
                <ul>
                    <li>Buat database baru dengan nama db_kantin</li>
                    <li>Import file SQL yang tersedia ke dalam database</li>
                </ul>
            </li>
            <li>
                <strong>Konfigurasi</strong>
                <ul>
                    <li>Buka file config/koneksi.php</li>
                    <li>Sesuaikan host, username, password, dan nama database</li>
                </ul>
            </li>
            <li>
                <strong>Jalankan Aplikasi</strong>
                <ul>
                    <li>Akses melalui browser: http://localhost/prj/SistemKantin/</li>
                    <li>Login menggunakan akun default yang tersedia</li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        <strong>Akun Default</strong>
        <ul>
            <li>
                <strong>Admin</strong>
                <ul>
                    <li>Username: admin</li>
                    <li>Password: admin123</li>
                </ul>
            </li>
            <li>
                <strong>Kasir</strong>
                <ul>
                    <li>Username: kasir1</li>
                    <li>Password: kasir123</li>
                </ul>
            </li>
            <li>
                <strong>Pembeli</strong>
                <ul>
                    <li>Username: pembeli1</li>
                    <li>Password: pembeli123</li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        <strong>Lisensi</strong>
        <ul>
            <li>Proyek ini menggunakan lisensi MIT.</li>
            <li>Dibangun untuk kemudahan operasional kantin.</li>
        </ul>
    </li>
</ul>