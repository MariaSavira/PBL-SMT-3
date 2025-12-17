<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengumuman Laboratorium</title>
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

    .skeleton-meta {
        height: 20px;
        width: 40%;
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

    .konten-pengumuman {
        line-height: 1.8;
        font-size: 16px;
        color: #2c3e50;
    }

    .konten-pengumuman p {
        margin-bottom: 20px;
    }

    .konten-pengumuman ul, 
    .konten-pengumuman ol {
        margin-bottom: 20px;
        padding-left: 30px;
    }

    .konten-pengumuman li {
        margin-bottom: 10px;
    }

    /* Badge untuk pengumuman */
    .pengumuman-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div id="header"></div>
 
  
  <section class="hero">
      <img src="../Assets/Image/Galeri-Berita/Heading.png" alt="Hero">
      <div class="hero-overlay"></div>
      <h1>Pengumuman Laboratorium</h1>
      <p>
          Informasi penting dan pengumuman terkini untuk seluruh anggota laboratorium
      </p>
  </section>

  
  <div class="container">
      
      <div class="content" id="pengumuman-content">
          
          <div class="skeleton skeleton-title"></div>
          <div class="skeleton skeleton-meta"></div>
          <div class="skeleton skeleton-text"></div>
          <div class="skeleton skeleton-text"></div>
          <div class="skeleton skeleton-text short"></div>
          <div class="skeleton skeleton-text"></div>
      </div>

      
      <aside class="sidebar">
          <h3>Pengumuman Terbaru</h3>
          <div id="pengumuman-sidebar">
              
              <div class="skeleton sidebar-skeleton"></div>
              <div class="skeleton sidebar-skeleton"></div>
              <div class="skeleton sidebar-skeleton"></div>
          </div>
      </aside>
  </div>

  <div id="footer"></div>
  <script src="../Assets/Javascript/HeaderFooter.js"></script>
  
  <script>
    
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

      function getPengumumanId() {
          const urlParams = new URLSearchParams(window.location.search);
          return urlParams.get('id');
      }

      function formatIsiPengumuman(isi) {
          if (!isi) return '';
          
          if (isi.includes('<p>') || isi.includes('<ul>') || isi.includes('<ol>')) {
              return isi;
          }
          
          return isi.split('\n\n').map(p => {
              if (p.trim()) {
                  return `<p>${p.replace(/\n/g, '<br>')}</p>`;
              }
              return '';
          }).join('');
      }

      async function loadPengumumanDetail() {
          const id = getPengumumanId();
          const contentDiv = document.getElementById('pengumuman-content');
          
          if (!id) {
              contentDiv.innerHTML = `
                  <div class="error-state">
                      <i class="fas fa-exclamation-triangle"></i>
                      <h4>ID Pengumuman Tidak Ditemukan</h4>
                      <p>Silakan kembali ke halaman berita.</p>
                      <a href="Berita.php" class="btn-back">
                          <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                      </a>
                  </div>
              `;
              return;
          }

          try {
              const response = await fetch(`../Admin/CRUD/Pengumuman_Lab/proses_pengumuman.php?action=get_data`);
              const result = await response.json();

              if (!result.success) {
                  contentDiv.innerHTML = `
                      <div class="error-state">
                          <i class="fas fa-exclamation-circle"></i>
                          <h4>Pengumuman Tidak Ditemukan</h4>
                          <p>${result.message || 'Pengumuman yang Anda cari tidak tersedia.'}</p>
                          <a href="Berita.php" class="btn-back">
                              <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                          </a>
                      </div>
                  `;
                  return;
              }

              const pengumuman = result.data.find(item => item.id_pengumuman == id);

              if (!pengumuman) {
                  contentDiv.innerHTML = `
                      <div class="error-state">
                          <i class="fas fa-exclamation-circle"></i>
                          <h4>Pengumuman Tidak Ditemukan</h4>
                          <p>Pengumuman yang Anda cari tidak tersedia.</p>
                          <a href="Berita.php" class="btn-back">
                              <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                          </a>
                      </div>
                  `;
                  return;
              }

              document.title = 'Pengumuman - Laboratorium';

              contentDiv.innerHTML = `
                  <span class="pengumuman-badge">
                      <i class="fas fa-bullhorn me-2"></i>Pengumuman
                  </span>
                  <h2 class="judul-berita">${pengumuman.isi.substring(0, 100)}...</h2>
                  <div class="meta">
                      <span><i class="far fa-calendar"></i> ${formatTanggalLengkap(pengumuman.tanggal_terbit)}</span>
                      <span><i class="far fa-user"></i> ${pengumuman.uploader}</span>
                  </div>
                  <div class="konten-pengumuman">${formatIsiPengumuman(pengumuman.isi)}</div>
              `;

          } catch (error) {
              console.error('Error:', error);
              contentDiv.innerHTML = `
                  <div class="error-state">
                      <i class="fas fa-exclamation-triangle"></i>
                      <h4>Terjadi Kesalahan</h4>
                      <p>Gagal memuat pengumuman. Silakan coba lagi nanti.</p>
                      <a href="Berita.php" class="btn-back">
                          <i class="fas fa-arrow-left me-2"></i>Kembali ke Berita
                      </a>
                  </div>
              `;
          }
      }

      async function loadPengumumanTerbaru() {
          const sidebar = document.getElementById('pengumuman-sidebar');
          
          try {
              const response = await fetch('../Admin/CRUD/Pengumuman_Lab/proses_pengumuman.php?action=get_data');
              const result = await response.json();
              
              sidebar.innerHTML = '';
              
              if (!result.success || result.data.length === 0) {
                  sidebar.innerHTML = '<p class="text-muted small">Belum ada pengumuman lainnya.</p>';
                  return;
              }

              const currentId = getPengumumanId();
              
              const pengumumanList = result.data
                  .filter(p => p.status === 'Aktif' && p.id_pengumuman != currentId)
                  .sort((a, b) => new Date(b.tanggal_terbit) - new Date(a.tanggal_terbit))
                  .slice(0, 5);

              if (pengumumanList.length === 0) {
                  sidebar.innerHTML = '<p class="text-muted small">Belum ada pengumuman lainnya.</p>';
                  return;
              }

              pengumumanList.forEach(pengumuman => {
                  const card = document.createElement('a');
                  card.href = `isi_pengumuman.php?id=${pengumuman.id_pengumuman}`;
                  card.className = 'card';
                  
                  const previewText = pengumuman.isi.length > 80 
                      ? pengumuman.isi.substring(0, 80) + '...' 
                      : pengumuman.isi;
                  
                  card.innerHTML = `
                      <span>${previewText}</span>
                      <i class="fa-solid fa-chevron-right arrow-icon"></i>
                  `;
                  sidebar.appendChild(card);
              });

          } catch (error) {
              console.error('Error loading sidebar:', error);
              sidebar.innerHTML = '<p class="text-muted small">Gagal memuat pengumuman terbaru.</p>';
          }
      }

      document.addEventListener('DOMContentLoaded', function() {
          loadPengumumanDetail();
          loadPengumumanTerbaru();
      });
  </script>
</body>
</html>