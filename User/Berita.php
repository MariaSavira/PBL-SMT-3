<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../Assets/Css/berita.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <title>Berita Laboratorium</title>
    <style>
        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-img {
            height: 200px;
            width: 100%;
            margin-bottom: 12px;
        }

        .skeleton-text {
            height: 16px;
            margin-bottom: 10px;
        }

        .skeleton-text.short {
            width: 60%;
        }

        /* Highlight Section Skeleton */
        .skeleton-highlight {
            height: 400px;
            border-radius: 16px;
        }

        /* Error State */
        .error-state {
            background: #fee2e2;
            color: #991b1b;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
        }

        .error-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    
    <!-- NAVBAR -->
    <div id="header"></div>
 
    <!-- HEADING -->
    <div class="heading">
        <h1>Berita Laboratorium</h1>
        <p style="margin-top: 16px; color: #fff;">
            Ikuti perkembangan terbaru seputar kegiatan laboratorium, pengumuman penting, 
            dan agenda riset yang sedang<br> berjalan. Halaman ini menjadi pusat informasi bagi 
            anggota, mitra, dan publik yang ingin mengetahui tren dan dinamika<br> analisis 
            bisnis berbasis data.
        </p>
    </div>

    <!-- HIGHLIGHT BERITA (Dinamis dari API - Berita Terbaru) -->
    <section class="highlight-floating">
        <div class="highlight-wrapper">
            <div class="highlight-card" id="highlight-berita">
                <!-- Skeleton Loading -->
                <div class="skeleton skeleton-highlight"></div>
            </div>
        </div>
    </section>

    <!-- SECTION BAWAH WRAPPER -->
    <section class="content-wrapper">

        <!-- PENGUMUMAN (Static - Manual Input) -->
        <div class="pengumuman-wrapper">
            <h3 class="judul-pengumuman">Pengumuman</h3>

            <div class="pengumuman">
                <div class="pengumuman-item">
                    <div class="icon">
                        <img src="../Assets/Image/Galeri-Berita/icon1.svg" alt="icon">
                    </div>
                    <div>
                        <h4>Pengumuman Persyaratan Bantuan UKT / SPP Tahun 2025</h4>
                        <p class="tanggal2">November 10, 2025</p>
                    </div>
                </div>

                <div class="pengumuman-item">
                    <div class="icon">
                        <img src="../Assets/Image/Galeri-Berita/icon2.svg" alt="icon">
                    </div>
                    <div>
                        <h4>BEASISWA UNGGULAN BAGI MASYARAKAT BERPRESTASI DAN PENYANDANG DISABILITAS 2025</h4>
                        <p class="tanggal">November 10, 2025</p>
                    </div>
                </div>

                <div class="pengumuman-item">
                    <div class="icon">
                        <img src="../Assets/Image/Galeri-Berita/icon3.svg" alt="icon">
                    </div>
                    <div>
                        <h4>Batas Pendaftaran dan Pelaksanaan Ujian Skripsi Tahap III Tahun Ajaran 2024/2025</h4>
                        <p class="tanggal">November 10, 2025</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BERITA TERKINI (Dinamis dari API) -->
        <div class="berita-terkini">
            <div class="berita-terkini-header">
                <h3>Berita Terkini</h3>
                <div class="filter-wrapper">
                    <div class="filter-select">
                        <select id="filterTag">
                            <option value="">Semua Tag</option>
                            <option value="prestasi">Prestasi</option>
                            <option value="kegiatan">Kegiatan</option>
                            <option value="kunjungan">Kunjungan</option>
                        </select>
                    </div>

                    <div class="filter-select">
                        <select id="filterTahun">
                            <option value="">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- LIST BERITA (Dinamis) -->
            <div id="berita-list">
                <!-- Skeleton Loading -->
                <div class="berita-card">
                    <div class="skeleton skeleton-img"></div>
                    <div class="text">
                        <div class="skeleton skeleton-text short"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                </div>
                <div class="berita-card">
                    <div class="skeleton skeleton-img"></div>
                    <div class="text">
                        <div class="skeleton skeleton-text short"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    
    <script>
        // Fungsi format tanggal Indonesia
        function formatTanggal(dateString) {
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const date = new Date(dateString);
            const day = date.getDate();
            const month = bulan[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${month} ${day}, ${year} | ${hours}:${minutes} WIB`;
        }

        // Fungsi truncate text
        function truncateText(text, maxLength) {
            if (!text) return '';
            const stripped = text.replace(/<[^>]*>/g, '');
            if (stripped.length <= maxLength) return stripped;
            return stripped.substr(0, maxLength) + '...';
        }

        // Load Highlight Berita (Berita Terbaru)
        async function loadHighlightBerita() {
            const container = document.getElementById('highlight-berita');
            
            try {
                const response = await fetch('../Admin/CRUD/Berita_Lab/api_berita.php?limit=1');
                const result = await response.json();
                
                if (!result.success || result.data.length === 0) {
                    container.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-newspaper"></i>
                            <p>Belum ada berita highlight tersedia</p>
                        </div>
                    `;
                    return;
                }
                
                const berita = result.data[0];
                const gambarUrl = berita.gambar 
                    ? `../Assets/Image/Galeri-Berita/${berita.gambar}` 
                    : '../Assets/Image/Galeri-Berita/default.jpg';
                
                container.innerHTML = `
                    <img src="${gambarUrl}" 
                         class="highlight-img" 
                         alt="${berita.judul}"
                         onerror="this.src='../Assets/Image/Galeri-Berita/default.jpg'" />
                    <div class="highlight-text">
                        <h2>${berita.judul}</h2>
                        <a href="isi_berita.html?id=${berita.id_berita}" class="btn-primary">Baca Selengkapnya</a>
                    </div>
                `;
                
            } catch (error) {
                console.error('Error loading highlight:', error);
                container.innerHTML = `
                    <div class="error-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Gagal memuat berita highlight</p>
                    </div>
                `;
            }
        }

        // Load List Berita Terkini
        async function loadBeritaTerkini() {
            const container = document.getElementById('berita-list');
            
            try {
                const response = await fetch('../Admin/CRUD/Berita_Lab/api_berita.php');
                const result = await response.json();
                
                container.innerHTML = '';
                
                if (!result.success) {
                    container.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>${result.message || 'Gagal memuat berita'}</p>
                        </div>
                    `;
                    return;
                }
                
                const beritaList = result.data;
                
                if (beritaList.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <h4>Belum Ada Berita</h4>
                            <p>Belum ada berita yang dipublikasikan</p>
                        </div>
                    `;
                    return;
                }
                
                // Skip berita pertama (sudah ditampilkan di highlight)
                const beritaToShow = beritaList.slice(1);
                
                beritaToShow.forEach(berita => {
                    const gambarUrl = berita.gambar 
                        ? `../Assets/Image/Galeri-Berita/${berita.gambar}` 
                        : '../Assets/Image/Galeri-Berita/default.jpg';
                    
                    const card = document.createElement('div');
                    card.className = 'berita-card';
                    card.style.cursor = 'pointer';
                    card.onclick = () => window.location.href = `isi_berita.html?id=${berita.id_berita}`;
                    
                    card.innerHTML = `
                        <div class="img" style="background-image: url('${gambarUrl}');"></div>
                        <div class="text">
                            <p class="tanggal">${formatTanggal(berita.tanggal)}</p>
                            <h4>${berita.judul}</h4>
                            <p>${truncateText(berita.isi, 120)}</p>
                            <span class="author">${berita.uploaded_by}</span>
                        </div>
                    `;
                    
                    container.appendChild(card);
                });
                
            } catch (error) {
                console.error('Error loading berita:', error);
                container.innerHTML = `
                    <div class="error-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Terjadi kesalahan saat memuat berita</p>
                    </div>
                `;
            }
        }

        // Filter functionality (optional - bisa dikembangkan nanti)
        document.addEventListener('DOMContentLoaded', function() {
            loadHighlightBerita();
            loadBeritaTerkini();
            
            // Event listener untuk filter (jika ingin diimplementasikan)
            document.getElementById('filterTag')?.addEventListener('change', function() {
                // Implementasi filter by tag
                console.log('Filter by tag:', this.value);
            });
            
            document.getElementById('filterTahun')?.addEventListener('change', function() {
                // Implementasi filter by tahun
                console.log('Filter by tahun:', this.value);
            });
        });
    </script>
</body>
</html>