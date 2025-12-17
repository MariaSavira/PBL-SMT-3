function getBeritaIdFromURL() {
  const params = new URLSearchParams(window.location.search);
  return params.get('id');
}

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

function displayBeritaDetail(berita) {

  const judulElement = document.querySelector('.judul-berita');
  if (judulElement) {
    judulElement.textContent = berita.judul;
  }

  const subjudulElement = document.querySelector('.subjudul');
  if (subjudulElement) {
    if (berita.subjudul) {
      subjudulElement.textContent = berita.subjudul;
      subjudulElement.style.display = 'block';
    } else {
      subjudulElement.style.display = 'none';
    }
  }

  const metaElement = document.querySelector('.meta');
  if (metaElement) {
    metaElement.innerHTML = `
      <span>${berita.tanggal} | ${berita.waktu}</span>
      <span>${berita.penulis}</span>
    `;
  }

  const bannerElement = document.querySelector('.banner');
  if (bannerElement) {
    bannerElement.src = berita.gambar;
    bannerElement.alt = berita.judul;
    bannerElement.onerror = function () {
      this.src = '../Assets/Image/default.jpg';
    };
  }

  const contentElement = document.querySelector('.content');
  if (contentElement && berita.konten) {
    const banner = contentElement.querySelector('.banner');
    if (banner) {
      let nextElement = banner.nextElementSibling;
      while (nextElement) {
        const toRemove = nextElement;
        nextElement = nextElement.nextElementSibling;
        toRemove.remove();
      }

      banner.insertAdjacentHTML('afterend', berita.konten);
    }
  }

  document.title = berita.judul + ' - Berita Laboratorium';
}

function displayBeritaTerbaru(beritaList, currentId) {
  const sidebar = document.querySelector('.sidebar');
  if (!sidebar) return;

  const beritaTerbaru = beritaList
    .filter(b => b.id != currentId && !b.isHighlight)
    .slice(0, 5);

  let cardsContainer = sidebar.querySelector('.sidebar-cards');
  if (!cardsContainer) {
    const h3 = sidebar.querySelector('h3');
    cardsContainer = document.createElement('div');
    cardsContainer.className = 'sidebar-cards';
    if (h3) {
      h3.insertAdjacentElement('afterend', cardsContainer);
    }

    const oldCards = sidebar.querySelectorAll('.card');
    oldCards.forEach(card => card.remove());
  }

  cardsContainer.innerHTML = '';

  if (beritaTerbaru.length === 0) {
    cardsContainer.innerHTML = '<p class="text-muted">Tidak ada berita lainnya</p>';
    return;
  }

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

document.addEventListener('DOMContentLoaded', async () => {
  const beritaId = getBeritaIdFromURL();

  if (!beritaId) {
    alert('Berita tidak ditemukan');
    window.location.href = 'Berita.html';
    return;
  }

  const berita = await loadBeritaDetail(beritaId);

  if (!berita) {
    alert('Berita tidak ditemukan');
    window.location.href = 'Berita.html';
    return;
  }

  displayBeritaDetail(berita);

  const beritaList = await loadBeritaList();
  displayBeritaTerbaru(beritaList, beritaId);
});