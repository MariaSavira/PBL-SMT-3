async function loadBerita(tag = null, tahun = null) {
  try {
    let url = '/PBL-SMT-3/Admin/CRUD/Berita_Lab/api_berita.php?action=list';

    if (tag) {
      url += '&tag=' + encodeURIComponent(tag);
    }

    if (tahun) {
      url += '&tahun=' + tahun;
    }

    const response = await fetch(url);
    const result = await response.json();

    if (result.success) {
      return result.data;
    } else {
      console.error('Error:', result.message);
      return [];
    }
  } catch (error) {
    console.error('Error loading berita:', error);
    return [];
  }
}

function displayHighlight(beritaList) {
  const highlightSection = document.querySelector('.highlight-wrapper');
  if (!highlightSection) return;

  const highlightBerita = beritaList.find(b => b.isHighlight);

  if (!highlightBerita) {
    highlightSection.innerHTML = '<p class="text-center">Tidak ada berita highlight</p>';
    return;
  }

  highlightSection.innerHTML = `
    <div class="highlight-card">
      <img src="${highlightBerita.gambar}" class="highlight-img" alt="${highlightBerita.judul}" onerror="this.src='../Assets/Image/default.jpg'" />
      <div class="highlight-text">
        <h2>${highlightBerita.judul}</h2>
        <a href="Isi_berita.html?id=${highlightBerita.id}" class="btn-primary">Baca Selengkapnya</a>
      </div>
    </div>
  `;
}

// Fungsi untuk menampilkan daftar berita
function displayBeritaList(beritaList) {
  const beritaContainer = document.querySelector('.berita-terkini');
  if (!beritaContainer) return;

  // Filter berita (tanpa highlight)
  const filteredBerita = beritaList.filter(b => !b.isHighlight);

  // Cari atau buat container untuk berita cards
  let beritaCardsContainer = document.querySelector('.berita-cards-container');

  if (!beritaCardsContainer) {
    const header = beritaContainer.querySelector('.berita-terkini-header');
    beritaCardsContainer = document.createElement('div');
    beritaCardsContainer.className = 'berita-cards-container';
    if (header && header.nextSibling) {
      beritaContainer.insertBefore(beritaCardsContainer, header.nextSibling);
    } else {
      beritaContainer.appendChild(beritaCardsContainer);
    }
  }

  // Kosongkan container
  beritaCardsContainer.innerHTML = '';

  if (filteredBerita.length === 0) {
    beritaCardsContainer.innerHTML = '<p class="text-center py-5">Tidak ada berita tersedia</p>';
    return;
  }

  // Tampilkan berita
  filteredBerita.forEach(berita => {
    const card = document.createElement('div');
    card.className = 'berita-card';
    card.innerHTML = `
      <div class="img" style="background-image: url('${berita.gambar}');"></div>
      <div class="text">
        <p class="tanggal">${berita.tanggal} | ${berita.waktu}</p>
        <h4>${berita.judul}</h4>
        <p>${berita.ringkasan}</p>
        <span class="author">${berita.penulis}</span>
      </div>
    `;

    // Tambahkan event listener untuk klik
    card.style.cursor = 'pointer';
    card.addEventListener('click', () => {
      window.location.href = `Isi_berita.html?id=${berita.id}`;
    });

    beritaCardsContainer.appendChild(card);
  });
}

// Fungsi untuk load tags
async function loadTags() {
  try {
    const response = await fetch('../Assets/Php/api_berita.php?action=tags');
    const result = await response.json();

    if (result.success) {
      return result.data;
    }
    return [];
  } catch (error) {
    console.error('Error loading tags:', error);
    return [];
  }
}

// Fungsi untuk load years
async function loadYears() {
  try {
    const response = await fetch('../Assets/Php/api_berita.php?action=years');
    const result = await response.json();

    if (result.success) {
      return result.data;
    }
    return [];
  } catch (error) {
    console.error('Error loading years:', error);
    return [];
  }
}

// Fungsi untuk setup filter
async function setupFilters() {
  const tagSelect = document.querySelector('.filter-select select:first-child');
  const tahunSelect = document.querySelector('.filter-select select:last-child');

  if (tagSelect) {
    const tags = await loadTags();
    tagSelect.innerHTML = '<option value="">Tag</option>' +
      tags.map(tag => `<option value="${tag}">${tag}</option>`).join('');

    tagSelect.addEventListener('change', async (e) => {
      const selectedTag = e.target.value || null;
      const selectedTahun = tahunSelect ? (tahunSelect.value || null) : null;
      const beritaList = await loadBerita(selectedTag, selectedTahun);
      displayBeritaList(beritaList);
    });
  }

  if (tahunSelect) {
    const years = await loadYears();
    tahunSelect.innerHTML = '<option value="">Tahun</option>' +
      years.map(y => `<option value="${y}">${y}</option>`).join('');

    tahunSelect.addEventListener('change', async (e) => {
      const selectedTahun = e.target.value || null;
      const selectedTag = tagSelect ? (tagSelect.value || null) : null;
      const beritaList = await loadBerita(selectedTag, selectedTahun);
      displayBeritaList(beritaList);
    });
  }
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', async () => {
  const beritaList = await loadBerita();
  displayHighlight(beritaList);
  displayBeritaList(beritaList);
  await setupFilters();
});