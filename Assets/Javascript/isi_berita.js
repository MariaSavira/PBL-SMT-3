// File: Assets/Javascript/isi-berita.js
// JavaScript untuk halaman Isi_berita.html (menggunakan database)

// Fungsi untuk mendapatkan parameter ID dari URL
function getBeritaIdFromURL() {
  const params = new URLSearchParams(window.location.search);
  return params.get('id');
}

// Fungsi untuk memuat detail berita dari API
async function loadBeritaDetail(id) {
  try {
    const response = await fetch(`../Assets/Php/api_berita.php?action=detail&id=${id}`);
    const result = await response.json();
    
    if (result.success) {
      return result.data;
    } else {
      throw new Error(result.message);
    }
  } catch (error) {
    console.error('Error loading berita detail:', error);
    return null;
  }
}

// Fungsi untuk memuat daftar berita (untuk sidebar)
async function loadBeritaList() {
  try {
    const response = await fetch('../Assets/Php/api_berita.php?action=list');
    const result = await response.json();
    
    if (result.success) {
      return result.data;
    }
    return [];
  } catch (error) {
    console.error('Error loading berita list:', error);
    return [];
  }
}

// Fungsi untuk menampilkan detail berita
function displayBeritaDetail(berita) {
  // Update judul
  const judulElement = document.querySelector('.judul-berita');
  if (judulElement) {
    judulElement.textContent = berita.judul;
  }

  // Update subjudul
  const subjudulElement = document.querySelector('.subjudul');
  if (subjudulElement) {
    if (berita.subjudul) {
      subjudulElement.textContent = berita.subjudul;
      subjudulElement.style.display = 'block';
    } else {
      subjudulElement.style.display = 'none';
    }
  }

  // Update meta (tanggal & penulis)
  const metaElement = document.querySelector('.meta');
  if (metaElement) {
    metaElement.innerHTML = `
      <span>${berita.tanggal} | ${berita.waktu}</span>
      <span>${berita.penulis}</span>
    `;
  }

  // Update banner image
  const bannerElement = document.querySelector('.banner');
  if (bannerElement) {
    bannerElement.src = berita.gambar;
    bannerElement.alt = berita.judul;
    bannerElement.onerror = function() {
      this.src = '../Assets/Image/default.jpg';
    };
  }

  // Update konten
  const contentElement = document.querySelector('.content');
  if (contentElement && berita.konten) {
    const banner = contentElement.querySelector('.banner');
    if (banner) {
      // Hapus semua elemen setelah banner
      let nextElement = banner.nextElementSibling;
      while (nextElement) {
        const toRemove = nextElement;
        nextElement = nextElement.nextElementSibling;
        toRemove.remove();
      }
      
      // Tambahkan konten baru
      banner.insertAdjacentHTML('afterend', berita.konten);
    }
  }

  // Update title halaman
  document.title = berita.judul + ' - Berita Laboratorium';
}

// Fungsi untuk menampilkan berita terbaru di sidebar
function displayBeritaTerbaru(beritaList, currentId) {
  const sidebar = document.querySelector('.sidebar');
  if (!sidebar) return;

  // Ambil berita terbaru (kecuali berita yang sedang dibaca)
  const beritaTerbaru = beritaList
    .filter(b => b.id != currentId && !b.isHighlight)
    .slice(0, 5);

  // Cari atau buat container untuk cards
  let cardsContainer = sidebar.querySelector('.sidebar-cards');
  if (!cardsContainer) {
    const h3 = sidebar.querySelector('h3');
    cardsContainer = document.createElement('div');
    cardsContainer.className = 'sidebar-cards';
    if (h3) {
      h3.insertAdjacentElement('afterend', cardsContainer);
    }
    
    // Hapus card lama yang ada di HTML
    const oldCards = sidebar.querySelectorAll('.card');
    oldCards.forEach(card => card.remove());
  }

  // Kosongkan container
  cardsContainer.innerHTML = '';

  if (beritaTerbaru.length === 0) {
    cardsContainer.innerHTML = '<p class="text-muted">Tidak ada berita lainnya</p>';
    return;
  }

  // Tampilkan berita terbaru
  beritaTerbaru.forEach(berita => {
    const card = document.createElement('a');
    card.href = `Isi_berita.html?id=${berita.id}`;
    card.className = 'card';
    card.innerHTML = `
      <span>${berita.judul}</span>
      <i class="fa-solid fa-chevron-right arrow-icon"></i>
    `;
    cardsContainer.appendChild(card);
  });
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', async () => {
  const beritaId = getBeritaIdFromURL();
  
  if (!beritaId) {
    alert('Berita tidak ditemukan');
    window.location.href = 'Berita.html';
    return;
  }

  // Load detail berita
  const berita = await loadBeritaDetail(beritaId);

  if (!berita) {
    alert('Berita tidak ditemukan');
    window.location.href = 'Berita.html';
    return;
  }

  displayBeritaDetail(berita);

  // Load berita lainnya untuk sidebar
  const beritaList = await loadBeritaList();
  displayBeritaTerbaru(beritaList, beritaId);
});