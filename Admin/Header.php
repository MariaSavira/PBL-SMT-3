<?php
    require_once __DIR__ . '/Cek_Autentikasi.php';
    require __DIR__ . '/Koneksi/KoneksiSasa.php';
?>
    <div class="content-header" id="header">
        <h1 class="header-title">Anggota Laboratorium</h1>

        <div class="profile-dropdown" id="profileDropdown">
            <button class="profile-toggle" type="button" id="profileToggle">
                <span class="profile-name"><?= htmlspecialchars($_SESSION['nama']) ?></span>

                    <?php
                        $folderUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
                        $folderFs  = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;

                        $foto = $_SESSION['foto'] ?? '';

                        if (!empty($foto) && file_exists($folderFs . $foto)) {
                            $src = $folderUrl . $foto;
                        } else {
                            $src = $folderUrl . 'No-Profile.png';
                        }
                    ?>

                <img src="<?= $src ?>" alt="Foto User" class="user-foto header-foto">

                <i class="fa-solid fa-chevron-down profile-arrow"></i>
            </button>

            <div class="profile-menu" id="profileMenu">
                <a href="../ProfilAdmin/EditProfil.php" class="profile-menu-item">
                    <i class="fa-regular fa-user"></i>
                    <span>Lihat Profil</span>
                </a>
                <a href="/PBL-SMT-3/Admin/Logout.php" class="profile-menu-item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>