<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require __DIR__ . '/../../Koneksi/KoneksiPDO.php';

$limit = 6;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// (optional) buat value di input search, pencarian tetap pakai JS
$search = trim($_GET['q'] ?? '');

$totalQuery = $db->query("SELECT COUNT(*) AS total FROM peminjaman_lab");
$totalData  = $totalQuery->fetch()['total'] ?? 0;
$totalPages = max(1, (int)ceil($totalData / $limit));

$stmt = $db->prepare("
    SELECT *
    FROM peminjaman_lab
    ORDER BY id_peminjaman DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Peminjaman Laboratorium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet"
    >

    <link rel="icon" type="images/x-icon"
          href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/PeminjamanLab.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <!-- ===== TOP CONTROLS (sama pola dengan IndexAnggotaLab) ===== -->
        <div class="top-controls">

            <!-- SEARCH (pakai ID sama untuk JS realtime) -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    id="search-input"
                    type="text"
                    placeholder="Cari"
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <!-- FILTER AREA – cuma visual, kalau mau bisa kamu isi JS filter status -->
            <div class="filter-area">
                <div class="filter-dropdown">
                    <button type="button" class="filter-toggle">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Status Peminjaman</span>
                        <i class="fa-solid fa-chevron-down caret"></i>
                    </button>

                    <div class="filter-menu">
                        <div class="filter-section">
                            <div class="filter-section-title">Status</div>

                            <!-- tombol ini bisa kamu pakai di JS untuk filter client-side -->
                            <button type="button" class="filter-item filter-status" data-status="">
                                Semua
                            </button>
                            <button type="button" class="filter-item filter-status" data-status="pending">
                                Pending
                            </button>
                            <button type="button" class="filter-item filter-status" data-status="disetujui">
                                Disetujui
                            </button>
                            <button type="button" class="filter-item filter-status" data-status="ditolak">
                                Ditolak
                            </button>
                        </div>
                    </div>
                </div>

                <a href="#" class="clear-filter" id="clearStatusFilter">
                    Hapus Filter
                </a>
            </div>

            <!-- ACTION BUTTONS (export + sort) -->
            <div class="right-actions">
                <a href="export.php" style="text-decoration:none;">
                    <button type="button" class="export">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        Export
                    </button>
                </a>

                <div class="sort-wrapper">
                    <button id="sort-btn" class="sort">
                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                        Urutkan : <strong id="sort-label">Default</strong>
                    </button>

                    <!-- Dropdown sort (JS-mu yang lama tetap bisa pakai ini) -->
                    <div id="sort-menu" class="sort-menu hidden">
                        <div data-sort="default">Default</div>
                        <div data-sort="latest">Terbaru</div>
                        <div data-sort="oldest">Terlama</div>
                        <div data-sort="az">Nama A–Z</div>
                        <div data-sort="za">Nama Z–A</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLE WRAPPER (stylenya disamakan dengan AnggotaLab) ===== -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Nama Peminjam</th>
                        <th>Email</th>
                        <th>Instansi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Pakai</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Approved</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!$result): ?>
                    <tr>
                        <td colspan="12">Belum ada data peminjaman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result as $row): ?>
                        <tr data-row-status="<?= htmlspecialchars($row['status']) ?>">
                            <td>
                                <input
                                    type="checkbox"
                                    class="row-check"
                                    value="<?= $row['id_peminjaman'] ?>">
                            </td>

                            <td><?= htmlspecialchars($row['id_peminjaman']) ?></td>
                            <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>

                            <td class="email-column">
                                <?= htmlspecialchars($row['email']) ?>
                            </td>

                            <td><?= htmlspecialchars($row['instansi']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pakai']) ?></td>
                            <td><?= htmlspecialchars($row['keperluan']) ?></td>

                            <td>
                                <select class="status-dropdown"
                                        data-id="<?= $row['id_peminjaman']; ?>">
                                    <option value="pending"
                                        <?= $row['status'] == "pending" ? "selected" : "" ?>>
                                        Pending
                                    </option>
                                    <option value="disetujui"
                                        <?= $row['status'] == "disetujui" ? "selected" : "" ?>>
                                        Disetujui
                                    </option>
                                    <option value="ditolak"
                                        <?= $row['status'] == "ditolak" ? "selected" : "" ?>>
                                        Ditolak
                                    </option>
                                </select>
                            </td>

                            <td class="approved-by">
                                <?= !empty($row['approved_by'])
                                    ? htmlspecialchars($row['approved_by'])
                                    : '-' ?>
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span id="catatan-text-<?= $row['id_peminjaman'] ?>">
                                        <?= $row['catatan_admin']
                                            ? htmlspecialchars($row['catatan_admin'])
                                            : '' ?>
                                    </span>

                                    <i class="fa-solid fa-pen-to-square edit-catatan"
                                       data-id="<?= $row['id_peminjaman'] ?>"
                                       style="cursor:pointer;"></i>

                                    <input type="hidden"
                                           id="catatan-input-<?= $row['id_peminjaman'] ?>"
                                           value="<?= $row['catatan_admin']
                                               ? htmlspecialchars($row['catatan_admin'])
                                               : '' ?>">
                                </div>
                            </td>

                            <td>
                                <button class="btn-save"
                                        data-id="<?= $row['id_peminjaman'] ?>">
                                    Simpan
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ===== MODAL CATATAN ===== -->
        <div id="catatan-modal" class="modal-overlay">
            <div class="modal-box">
                <h3 class="modal-title">Edit Catatan</h3>

                <textarea id="catatan-modal-text"
                          class="modal-textarea"></textarea>

                <div class="modal-actions">
                    <button id="catatan-modal-cancel" class="btn-cancel">
                        Batal
                    </button>
                    <button id="catatan-modal-save" class="btn-save">
                        Simpan Catatan
                    </button>
                </div>
            </div>
        </div>

        <!-- ===== FOOTER (hapus terpilih + pagination ala AnggotaLab) ===== -->
        <div class="table-footer">
            <div class="delete-selection" style="cursor: pointer">
                <i class="fa-solid fa-trash"></i>
                Hapus data yang dipilih
            </div>

            <div class="pagination">
                <?php
                // show previous
                if ($page > 1) {
                    echo "<a href='?page=" . ($page - 1) . "' class='page-link prev'>&laquo; Sebelumnya</a>";
                }

                // Always show page 1
                if ($page > 3) {
                    echo "<a href='?page=1' class='page-link'>1</a>";
                    echo "<span class='dots'>...</span>";
                }

                // Middle pages (page-1, page, page+1)
                for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++) {
                    echo "<a href='?page=$i' class='page-link " . ($i == $page ? "active" : "") . "'>$i</a>";
                }

                // Always show last page
                if ($page < $totalPages - 2) {
                    echo "<span class='dots'>...</span>";
                    echo "<a href='?page=$totalPages' class='page-link'>$totalPages</a>";
                }

                // next
                if ($page < $totalPages) {
                    echo "<a href='?page=" . ($page + 1) . "' class='page-link next'>Berikutnya &raquo;</a>";
                }
                ?>
            </div>
        </div>
    </main>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/PeminjamanLab.js"></script>
</body>
</html>
