<?php
require 'koneksi.php';

// tampilkan error tapi sembunyikan DEPRECATED
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

// search & filter
$search   = isset($_GET['q']) ? trim($_GET['q']) : "";
$filterKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

// Ambil daftar kategori dari database
$kategori_options = [];
$res_kategori = pg_query($conn, "SELECT id_kategori, nama_kategori FROM kategori_karya ORDER BY nama_kategori");

// Tambahkan pengecekan error untuk res_kategori
if ($res_kategori) {
    while ($row = pg_fetch_assoc($res_kategori)) {
        $kategori_options[$row['id_kategori']] = $row['nama_kategori'];
    }
} // Tidak perlu else jika error hanya ditangkap oleh pg_query warning/fatal error berikutnya.

// query dasar - join untuk menampilkan nama kategori
$sql = "
  SELECT 
    k.id_karya, k.judul, k.deskripsi, k.link, k.uploaded_at, k.uploaded_by,
    c.nama_kategori
  FROM 
    karya k
  LEFT JOIN 
    kategori_karya c ON k.id_kategori = c.id_kategori
  WHERE 
    1=1
";

if ($search !== "") {
    $s = pg_escape_string($conn, $search);
    $sql .= " AND (k.judul ILIKE '%$s%' OR k.deskripsi ILIKE '%$s%' OR c.nama_kategori ILIKE '%$s%' OR k.uploaded_by::text ILIKE '%$s%')";
}

if ($filterKategori > 0) {
    if (array_key_exists($filterKategori, $kategori_options)) {
        $sql .= " AND k.id_kategori = $filterKategori";
    } else {
        $filterKategori = 0;
    }
}

$sql .= " ORDER BY k.uploaded_at DESC";

$result = pg_query($conn, $sql);
$total = $result ? pg_num_rows($result) : 0;

// Menentukan label kategori yang sedang aktif
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Karya</title>
    <link rel="stylesheet" href="../../../../Assets/Css/Admin/KaryaAdmin.css">
    <style>
        /* Gaya tambahan untuk kolom Karya */
        .karya-table .col-judul {
            width: 200px;
        }

        .karya-table .col-deskripsi {
            width: 280px;
        }

        .karya-table .col-kategori {
            width: 150px;
        }

        .karya-table .col-author {
            width: 120px;
        }

        /* Gaya baru untuk Filter Icon Dropdown */
        .filter-kategori-dropdown {
            /* Menerapkan gaya visual yang mirip dengan tombol 'Aksi' tetapi di toolbar */
            position: relative;
            display: inline-block;
            margin-right: 10px;
            /* Jarak dengan chip */
        }

        .filter-kategori-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 8px 8px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .filter-kategori-dropdown .dropdown-toggle:hover {
            background-color: #e5e7eb;
            border-color: #9ca3af;
        }

        .filter-kategori-dropdown .filter-icon {
            font-size: 1.1em;
            /* Menggunakan Unicode '☰' sebagai pengganti icon sliders */
        }

        .filter-kategori-dropdown .dropdown-menu {
            /* Agar menu drop ke bawah dengan benar */
            left: 0;
            right: auto;
            min-width: 180px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="brand">
            <div class="brand-logo"></div>
            <div class="brand-text">
                Laboratorium<br><span>Business Analytics</span>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>Karya</h1>
            <div class="user-info">
                <div>Maria Savira</div>
                <div class="user-avatar"></div>
            </div>
        </div>

        <form class="toolbar" method="get" action="">
            <div class="toolbar-left">

                <div class="search-wrapper">
                    <div class="search-box">
                        <span class="search-icon">
                            🔍
                        </span>
                        <input
                            type="text"
                            name="q"
                            class="search-input"
                            placeholder="Cari Judul, Deskripsi, atau Kategori"
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <span class="search-count">
                        <strong style="color: #222;"><?php echo $total; ?></strong> hasil
                    </span>
                </div>

                <div class="filter-line">
                    <div class="action-dropdown filter-kategori-dropdown">
                        <button type="button" class="dropdown-toggle" title="Filter Kategori">
                            <span class="filter-icon">&#9776;</span>
                        </button>
                        <div class="dropdown-menu" id="kategoriFilterMenu">
                            <a href="index.php?q=<?php echo urlencode($search); ?>"
                                class="dropdown-item <?php echo ($filterKategori == 0) ? 'active' : ''; ?>">
                                Semua Kategori
                            </a>
                            <?php foreach ($kategori_options as $id_kat => $nama_kat): ?>
                                <a href="index.php?q=<?php echo urlencode($search); ?>&kategori=<?php echo $id_kat; ?>"
                                    class="dropdown-item <?php echo ($filterKategori == $id_kat) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($nama_kat); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (!empty($filterKategori)): ?>
                        <div class="chip chip-active">
                            <?php echo htmlspecialchars($kategori_options[$filterKategori] ?? 'Tidak Ditemukan'); ?>
                            <a href="index.php?q=<?php echo htmlspecialchars($search); ?>" class="chip-close">×</a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($filterKategori) || !empty($search)): ?>
                        <a href="index.php" class="filter-reset">Hapus Filter</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="toolbar-right">
                <div class="toolbar-right-top">
                    <button type="button" class="btn-export">
                        <span class="export-icon">
                            <svg width="27" height="25" viewBox="0 0 27 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M25.9364 3.08333L23.8822 0.75C23.4684 0.291667 22.8624 0 22.1679 0H4.43357C3.73898 0 3.13306 0.291667 2.73404 0.763889L0.679814 3.08333C0.251236 3.56944 0 4.18056 0 4.86111V22.2222C0 23.75 1.31529 25 2.95571 25H23.6457C25.2714 25 26.6014 23.75 26.6014 22.2222V4.86111C26.6014 4.18056 26.3502 3.56944 25.9364 3.08333ZM13.3007 9.02778L21.4289 16.6667H16.2564V19.4444H10.345V16.6667H5.1725L13.3007 9.02778ZM3.13306 2.77778L4.3449 1.38889H22.0792L23.4536 2.77778H3.13306Z" fill="#1E5AA8" />
                            </svg>
                        </span>
                        <span>Export</span>
                    </button>

                    <button type="button" class="btn-sort">
                        <span class="sort-icon">
                            <svg width="12" height="12" viewBox="0 0 21 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 15H7V12.5H0V15ZM0 0V2.5H21V0H0ZM0 8.75H14V6.25H7H0V8.75Z" fill="#585555" />
                            </svg>
                        </span>
                        <span>Urutkan : <span class="sort-label">Default</span></span>
                    </button>

                    <a href="tambah_edit.php" class="btn btn-primary btn-add">
                        <span class="icon">
                            <svg width="10" height="10" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.2398 7.27218H7.39176V12.2402H4.84776V7.27218H-0.000242054V4.96818H4.84776V0.000178337H7.39176V4.96818H12.2398V7.27218Z" fill="white" />
                            </svg>
                        </span> Tambah
                    </a>
                </div>

                <div class="pagination-mini">
                    <span>1 of 9</span>
                    <button type="button" class="page-arrow disabled">‹</button>
                    <button type="button" class="page-arrow">›</button>
                </div>
            </div>
        </form>

        <form action="hapus.php" method="post">
            <div class="table-wrapper">
                <table class="pub-table karya-table">
                    <thead>
                        <tr>
                            <th class="col-check">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>id_karya</th>
                            <th class="col-judul">judul</th>
                            <th class="col-deskripsi">deskripsi</th>
                            <th class="col-kategori">kategori</th>
                            <th class="col-author">uploaded_by</th>
                            <th class="col-aksi">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($total == 0): ?>
                            <tr>
                                <td colspan="7" class="no-data">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = pg_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="col-check">
                                        <input type="checkbox" name="ids[]" value="<?php echo $row['id_karya']; ?>">
                                    </td>

                                    <td><?php echo htmlspecialchars($row['id_karya']); ?></td>

                                    <td class="col-judul">
                                        <?php echo htmlspecialchars(substr($row['judul'], 0, 50)) . (strlen($row['judul']) > 50 ? '...' : ''); ?>
                                    </td>

                                    <td class="col-deskripsi">
                                        <?php echo htmlspecialchars(substr($row['deskripsi'], 0, 70)) . (strlen($row['deskripsi']) > 70 ? '...' : ''); ?>
                                    </td>

                                    <td class="col-kategori">
                                        <span class="badge badge-jenis">
                                            <?php echo htmlspecialchars($row['nama_kategori'] ?? 'N/A'); ?>
                                        </span>
                                    </td>

                                    <td class="col-author">
                                        <?php echo htmlspecialchars($row['uploaded_by'] ?? 'Anonim'); ?>
                                    </td>

                                    <td class="col-aksi">
                                        <div class="action-dropdown">
                                            <button type="button" class="dropdown-toggle" title="Aksi">
                                                ...
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="tambah_edit.php?id=<?php echo $row['id_karya']; ?>" class="dropdown-item">Edit</a>
                                                <a href="hapus.php?id=<?php echo $row['id_karya']; ?>"
                                                    class="dropdown-item"
                                                    onclick="return confirm('Yakin ingin hapus Karya ID <?php echo $row['id_karya']; ?>?')">Hapus</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="table-footer">
                    <button type="submit" class="btn btn-delete footer-delete-btn" onclick="return confirm('Yakin ingin menghapus data yang dipilih?')">
                        <svg width="20" height="20" viewBox="0 0 27 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25 0H1.92308C0.860991 0 0 0.860991 0 1.92308V2.88461C0 3.9467 0.860991 4.80769 1.92308 4.80769H25C26.0621 4.80769 26.9231 3.9467 26.9231 2.88461V1.92308C26.9231 0.860991 26.0621 0 25 0Z" fill="#144B89" />
                            <path d="M2.55124 6.73096C2.48367 6.7306 2.4168 6.74448 2.35496 6.7717C2.29312 6.79891 2.23772 6.83886 2.19235 6.88893C2.14699 6.939 2.11269 6.99807 2.09169 7.06228C2.07069 7.1265 2.06346 7.19442 2.07047 7.26161L3.6516 22.4395C3.65127 22.4439 3.65127 22.4483 3.6516 22.4527C3.73421 23.1547 4.07171 23.802 4.60004 24.2716C5.12836 24.7412 5.81072 25.0005 6.51758 25.0002H20.4052C21.1119 25.0002 21.7939 24.7408 22.322 24.2712C22.8501 23.8016 23.1874 23.1545 23.27 22.4527V22.4401L24.8487 7.26161C24.8557 7.19442 24.8485 7.1265 24.8275 7.06228C24.8065 6.99807 24.7722 6.939 24.7268 6.88893C24.6815 6.83886 24.6261 6.79891 24.5642 6.7717C24.5024 6.74448 24.4355 6.7306 24.3679 6.73096H2.55124ZM17.5068 17.5897C17.5982 17.6786 17.671 17.7847 17.721 17.9019C17.7711 18.0191 17.7973 18.1451 17.7982 18.2725C17.7991 18.4 17.7747 18.5264 17.7263 18.6443C17.6779 18.7622 17.6066 18.8693 17.5165 18.9594C17.4263 19.0495 17.3192 19.1208 17.2012 19.1691C17.0833 19.2174 16.9569 19.2418 16.8295 19.2409C16.702 19.2399 16.576 19.2136 16.4588 19.1635C16.3416 19.1134 16.2356 19.0406 16.1468 18.9491L13.4617 16.264L10.776 18.9491C10.5948 19.1251 10.3517 19.2228 10.0991 19.221C9.84655 19.2192 9.60482 19.1181 9.42618 18.9395C9.24754 18.761 9.14633 18.5193 9.14443 18.2667C9.14253 18.0141 9.24008 17.771 9.41602 17.5897L12.1017 14.904L9.41602 12.2183C9.24008 12.0371 9.14253 11.7939 9.14443 11.5414C9.14633 11.2888 9.24754 11.0471 9.42618 10.8685C9.60482 10.69 9.84655 10.5889 10.0991 10.5871C10.3517 10.5853 10.5948 10.683 10.776 10.859L13.4617 13.5441L16.1468 10.859C16.3279 10.683 16.5711 10.5853 16.8237 10.5871C17.0762 10.5889 17.318 10.69 17.4966 10.8685C17.6752 11.0471 17.7764 11.2888 17.7783 11.5414C17.7803 11.7939 17.6827 12.0371 17.5068 12.2183L14.8211 14.904L17.5068 17.5897Z" fill="#144B89" />
                        </svg> Hapus data yang dipilih
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('checkAll')?.addEventListener('change', function() {
            const boxes = document.querySelectorAll('input[name="ids[]"]');
            boxes.forEach(cb => cb.checked = this.checked);
        });

        // Skrip JavaScript ini sekarang juga menangani dropdown filter kategori
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const menu = this.nextElementSibling;

                // Tutup semua menu dropdown yang lain
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    if (m !== menu) {
                        m.classList.remove('active');
                    }
                });

                // Toggle menu yang sedang diklik
                menu.classList.toggle('active');
            });
        });

        // Tutup dropdown jika mengklik di luar menu
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    m.classList.remove('active');
                });
            }
        });
    </script>

</body>

</html>