<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Berita Laboratorium</title>
  <link rel="stylesheet" href="../Assets/Css/isi_berita.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
  <style>
    /* Loading Skeleton */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-title {
        height: 40px;
        width: 80%;
        margin-bottom: 20px;
    }

    .skeleton-subtitle {
        height: 25px;
        width: 60%;
        margin-bottom: 15px;
    }

    .skeleton-meta {
        height: 20px;
        width: 40%;
        margin-bottom: 20px;
    }

    .skeleton-img {
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }

    .skeleton-text {
        height: 18px;
        margin-bottom: 12px;
    }

    .skeleton-text.short {
        width: 70%;
    }

    .sidebar-skeleton {
        height: 80px;
        margin-bottom: 15px;
    }

    /* Error State */
    .error-state {
        background: #fee2e2;
        color: #991b1b;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        margin: 40px auto;
    }

    .error-state i {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .btn-back {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 28px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #2563eb;
        color: white;
        transform: translateY(-2px);
    }

    .konten-berita {
        line-height: 1.8;
        font-size: 16px;
        color: #2c3e50;
    }

    .konten-berita p {
        margin-bottom: 20px;
    }

    .konten-berita ul, 
    .konten-berita ol {
        margin-bottom: 20px;
        padding-left: 30px;
    }

    .konten-berita li {
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div id="header"></div>
 
  <!-- HERO SECTION -->
  <section class="hero">
      <img src="../Assets/Image/Galeri-Berita/Heading.png" alt="Hero">
      <div class="hero-overlay"></div>
      <h1>Berita Laboratorium</h1>
      <p>
          Ikuti perkembangan terbaru seputar kegiatan laboratorium, pengumuman penting, 
          dan agenda riset yang sedang berjalan.
      </p>
  </section>

  <!-- MAIN CONTENT -->
  <div class="container">
      <!-- KONTEN BERITA -->
      <div class="content" id="berita-content">
          <!-- Skeleton Loading -->
          <div class="skeleton skeleton-title"></div>
          <div class="skeleton skeleton-subtitle"></div>
          <div class="skeleton skeleton-meta"></div>
          <div class="skeleton skeleton-img"></div>
          <div class="skeleton skeleton-text"></div>
          <div class="skeleton skeleton-text"></div>
          <div class="skeleton skeleton-text short"></div>
          <div class="skeleton skeleton-text"></div>
      </div>

      <!-- SIDEBAR -->
      <aside class="sidebar">
          <h3>Berita Terbaru</h3>
          <div id="berita-sidebar">
              <!-- Skeleton Loading -->
              <div class="skeleton sidebar-skeleton"></div>
              <div class="skeleton sidebar-skeleton"></div>
              <div class="skeleton sidebar-skeleton"></div>
          </div>
      </aside>
  </div>

  <div id="footer"></div>
  <script src="../Assets/Javascript/HeaderFooter.js"></script>
  
  <script>
      // Fungsi format tanggal lengkap
      function formatTanggalLengkap(dateString) {
          const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
          const date = new Date(dateString);
          const day = date.getDate();
          const month = bulan[date.getMonth()];
          const year = date.getFullYear();
          const hours = String(date.getHours()).padStart(2, '0');
          const minutes = String(date.getMinutes()).padStart(2, '0');
          
          return `${day} ${month} ${year} | ${hours}:${minutes} WIB`;
      }

      // Get ID dari URL
      function getBeritaId() {
          const urlParams = new URLSearchParams(window.location.search);
          return urlParams.get('id');
      }

      // Format isi berita
      function formatIsiBerita(isi) {
          if (!isi) return '';
          
          // Jika sudah ada HTML tags, kembalikan apa adanya
          if (isi.includes('<p>') || isi.includes('<ul>') || isi.includes('<ol>')) {
              return isi;
          }
          
          // Jika plain text, convert newlines to <br>
          return isi.split('\n\n').map(p => {
              if (p.trim()) {
                  return `<p>${p.replace(/\n/g, '<br>')}</p>`;
              }
              return '';
          }).join('');
      }

      // Load berita detail
      async function loadBeritaDetail() {
          const id = getBeritaId();
          const contentDiv = document.getElementById('berita-content');
          
          if (!id) {
              contentDiv.innerHTML = `
                  <div class="error-state">
                      <i class="fas fa-exclamation-triangle"></i>
                      <h4>ID Berita Tidak Ditemukan</h4>
                      <p>Silakan kembali ke halaman berita.</p>
                      <a href="Berita.php" class="btn-back">
                          <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                      </a>
                  </div>
              `;
              return;
          }

          try {
              const response = await fetch(`../Admin/CRUD/Berita_Lab/api_berita.php?id=${id}`);
              const result = await response.json();

              if (!result.success || !result.data) {
                  contentDiv.innerHTML = `
                      <div class="error-state">
                          <i class="fas fa-exclamation-circle"></i>
                          <h4>Berita Tidak Ditemukan</h4>
                          <p>${result.message || 'Berita yang Anda cari tidak tersedia.'}</p>
                          <a href="Berita.php" class="btn-back">
                              <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                          </a>
                      </div>
                  `;
                  return;
              }

              const berita = result.data;

              // Update title halaman
              document.title = berita.judul + ' - Berita Laboratorium';

              // Tampilkan konten berita
              const gambarUrl = berita.gambar 
                  ? `../Assets/Image/Galeri-Berita/${berita.gambar}` 
                  : '../Assets/Image/Galeri-Berita/default.jpg';

              contentDiv.innerHTML = `
                  <h2 class="judul-berita">${berita.judul}</h2>
                  ${berita.subjudul ? `<div class="subjudul">${berita.subjudul}</div>` : ''}
                  <div class="meta">
                      <span><i class="far fa-calendar"></i> ${formatTanggalLengkap(berita.tanggal)}</span>
                      <span><i class="far fa-user"></i> ${berita.uploaded_by}</span>
                  </div>
                  <img src="${gambarUrl}" 
                       class="banner" 
                       alt="${berita.judul}"
                       onerror="this.src='../Assets/Image/Galeri-Berita/default.jpg'">
                  <div class="konten-berita">${formatIsiBerita(berita.isi)}</div>
              `;

          } catch (error) {
              console.error('Error:', error);
              contentDiv.innerHTML = `
                  <div class="error-state">
                      <i class="fas fa-exclamation-triangle"></i>
                      <h4>Terjadi Kesalahan</h4>
                      <p>Gagal memuat berita. Silakan coba lagi nanti.</p>
                      <a href="Berita.php" class="btn-back">
                          <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                      </a>
                  </div>
              `;
          }
      }

      // Load berita terbaru untuk sidebar
      async function loadBeritaTerbaru() {
          const sidebar = document.getElementById('berita-sidebar');
          
          try {
              const response = await fetch('../Admin/CRUD/Berita_Lab/api_berita.php?limit=6');
              const result = await response.json();
              
              sidebar.innerHTML = '';
              
              if (!result.success || result.data.length === 0) {
                  sidebar.innerHTML = '<p class="text-muted small">Belum ada berita lainnya.</p>';
                  return;
              }

              const currentId = getBeritaId();
              const beritaList = result.data;
              
              // Filter berita (exclude current)
              const otherBerita = beritaList.filter(b => b.id_berita != currentId).slice(0, 5);

              if (otherBerita.length === 0) {
                  sidebar.innerHTML = '<p class="text-muted small">Belum ada berita lainnya.</p>';
                  return;
              }

              otherBerita.forEach(berita => {
                  const card = document.createElement('a');
                  card.href = `isi_berita.php?id=${berita.id_berita}`;
                  card.className = 'card';
                  card.innerHTML = `
                      <span>${berita.judul.length > 100 ? berita.judul.substring(0, 100) + '...' : berita.judul}</span>
                      <i class="fa-solid fa-chevron-right arrow-icon"></i>
                  `;
                  sidebar.appendChild(card);
              });

          } catch (error) {
              console.error('Error loading sidebar:', error);
              sidebar.innerHTML = '<p class="text-muted small">Gagal memuat berita terbaru.</p>';
          }
      }

      // Load saat halaman dimuat
      document.addEventListener('DOMContentLoaded', function() {
          loadBeritaDetail();
          loadBeritaTerbaru();
      });
  </script>
</body>
</html>