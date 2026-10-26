/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: assets/js/app.js
 * 
 * Pengendali Client-side:
 * 1. SPA Router / Ajax Fetch Loader dengan Browser History (pushState/popstate)
 * 2. Loading State & Error State dengan Retry Callback
 * 3. Fitur Pencarian Real-Time (Nama & Jabatan) dan Filter Kategori
 * 4. Validasi Form Tambah Guru/Staf (Client-Side & Server Feedback)
 * 5. Pengiriman Form via Fetch API POST ke simpan-guru.php
 */

'use strict';

(function () {
  // --- Cache DOM Elements ---
  const mainContent = document.getElementById('main-content');
  const navLinks = document.querySelectorAll('.nav-link');
  const mobileToggle = document.getElementById('mobile-toggle');
  const navMenu = document.getElementById('nav-menu');

  // Simpan konten default Beranda untuk navigasi instan tanpa request ulang jika diperlukan
  let homeContentBackup = '';

  // Inisialisasi saat DOM siap
  document.addEventListener('DOMContentLoaded', () => {
    // Simpan template Beranda awal
    if (mainContent) {
      homeContentBackup = mainContent.innerHTML;
    }

    // Pasang event listener untuk navigasi
    initNavigation();

    // Pasang toggle menu mobile
    initMobileMenu();

    // Deteksi URL hash awal atau load dari history
    handleInitialRoute();

    // Listener tombol browser Back / Forward
    window.addEventListener('popstate', (event) => {
      if (event.state && event.state.view) {
        navigateTo(event.state.view, false);
      } else {
        navigateTo('beranda', false);
      }
    });
  });

  /* ==========================================================================
     1. SISTEM NAVIGASI AJAX & SPA ROUTER
     ========================================================================== */

  function initNavigation() {
    document.addEventListener('click', (e) => {
      // Cek apakah target atau parent target adalah link navigasi dengan data-view
      const navTarget = e.target.closest('[data-view]');
      if (!navTarget) return;

      e.preventDefault();
      const targetView = navTarget.getAttribute('data-view');
      navigateTo(targetView, true);

      // Tutup menu mobile jika sedang terbuka
      if (navMenu && navMenu.classList.contains('is-open')) {
        navMenu.classList.remove('is-open');
      }
    });
  }

  function initMobileMenu() {
    if (!mobileToggle || !navMenu) return;

    mobileToggle.addEventListener('click', () => {
      const isOpen = navMenu.classList.toggle('is-open');
      mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  function handleInitialRoute() {
    const pathname = window.location.pathname.toLowerCase();
    const isGuruStafPage = pathname.endsWith('guru-staf.php');
    const isTambahGuruPage = pathname.endsWith('tambah-guru.php');

    // Jika dibuka langsung via URL file guru-staf.php
    if (isGuruStafPage) {
      updateActiveNav('guru-staf');
      initDirectoryFeatures();
      return;
    }

    // Jika dibuka langsung via URL file tambah-guru.php
    if (isTambahGuruPage) {
      updateActiveNav('tambah-guru');
      initFormFeatures();
      return;
    }

    // Berada di index.html atau root /TugasWeb_GuruStaf/
    const hash = window.location.hash.replace('#', '');
    if (hash === 'guru-staf' || hash === 'tambah-guru') {
      navigateTo(hash, false);
    } else {
      updateActiveNav('beranda');
      if (window.history && window.history.replaceState) {
        history.replaceState({ view: 'beranda' }, '', window.location.pathname);
      }
    }
  }

  /**
   * Fungsi inti navigasi halaman via Ajax
   * @param {string} view - 'beranda', 'guru-staf', atau 'tambah-guru'
   * @param {boolean} push - Apakah perlu menyimpan riwayat ke history
   */
  async function navigateTo(view, push = true) {
    updateActiveNav(view);

    if (push) {
      const newHash = view === 'beranda' ? '' : '#' + view;
      history.pushState({ view: view }, '', window.location.pathname + newHash);
    }

    if (view === 'beranda') {
      if (document.getElementById('hero-beranda') || (homeContentBackup && homeContentBackup.includes('hero-beranda'))) {
        renderBeranda();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        window.location.href = 'index.html';
      }
      return;
    }

    const endpointMap = {
      'guru-staf': 'guru-staf.php',
      'tambah-guru': 'tambah-guru.php'
    };

    const targetUrl = endpointMap[view];
    if (!targetUrl) return;

    // Tampilkan Loading State
    renderLoadingState(view);

    try {
      const response = await fetch(targetUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        throw new Error(`Server merespon dengan status ${response.status} (${response.statusText})`);
      }

      const htmlFragment = await response.text();
      mainContent.innerHTML = htmlFragment;

      // Inisialisasi fitur spesifik fragmen yang dimuat
      if (view === 'guru-staf') {
        initDirectoryFeatures();
      } else if (view === 'tambah-guru') {
        initFormFeatures();
      }

      // Scroll halus ke area konten
      mainContent.scrollIntoView({ behavior: 'smooth', block: 'start' });

    } catch (err) {
      console.error('[Ajax Navigation Error]', err);
      renderErrorState(view, targetUrl, err.message);
    }
  }

  function updateActiveNav(activeView) {
    navLinks.forEach((link) => {
      const linkView = link.getAttribute('data-view');
      if (linkView === activeView) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      } else {
        link.classList.remove('active');
        link.removeAttribute('aria-current');
      }
    });
  }

  function renderBeranda() {
    mainContent.innerHTML = homeContentBackup;
  }

  function renderLoadingState(view) {
    const label = view === 'guru-staf' ? 'Direktori Guru dan Staf' : 'Form Tambah Data';
    mainContent.innerHTML = `
      <div class="container">
        <div class="loading-box" role="status" aria-live="polite">
          <div class="spinner"></div>
          <p class="loading-text">Memuat data ${label}...</p>
        </div>
      </div>
    `;
  }

  function renderErrorState(view, targetUrl, errorMsg) {
    mainContent.innerHTML = `
      <div class="container view-container">
        <div class="alert alert-danger" role="alert">
          <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
            <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
          </svg>
          <div class="alert-content">
            <h4 style="font-weight:700; margin-bottom:0.25rem;">Gagal Memuat Konten</h4>
            <p>Terjadi kendala saat memuat fragmen <code>${targetUrl}</code>. Pastikan Apache & MySQL di XAMPP aktif.</p>
            <p style="font-size:0.85rem; color:#991b1b; margin-top:0.4rem;">Detail Error: ${escapeHtml(errorMsg)}</p>
            <div class="alert-actions">
              <button type="button" class="btn btn-sm btn-primary" id="btn-retry-view">Coba Lagi</button>
              <a href="${targetUrl}" class="btn btn-sm btn-secondary" target="_blank" rel="noopener">Buka Fallback Langsung</a>
            </div>
          </div>
        </div>
      </div>
    `;

    const retryBtn = document.getElementById('btn-retry-view');
    if (retryBtn) {
      retryBtn.addEventListener('click', () => navigateTo(view, false));
    }
  }

  /* ==========================================================================
     2. FITUR DIREKTORI (PENCARIAN & FILTER KATEGORI)
     ========================================================================== */

  function initDirectoryFeatures() {
    const searchInput = document.getElementById('search-guru');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.person-card');
    const counterDisplay = document.getElementById('counter-display');
    const emptyState = document.getElementById('empty-search-state');

    if (!searchInput || cards.length === 0) return;

    let activeFilter = 'Semua';
    let searchQuery = '';

    function applyFilterAndSearch() {
      const q = searchQuery.toLowerCase().trim();
      let visibleCount = 0;

      cards.forEach((card) => {
        const nama = (card.getAttribute('data-nama') || '').toLowerCase();
        const jabatan = (card.getAttribute('data-jabatan') || '').toLowerCase();
        const jenis = card.getAttribute('data-jenis') || '';

        const matchCategory = activeFilter === 'Semua' || jenis.toLowerCase() === activeFilter.toLowerCase();
        const matchSearch = q === '' || nama.includes(q) || jabatan.includes(q);

        if (matchCategory && matchSearch) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      // Update counter
      if (counterDisplay) {
        counterDisplay.textContent = `Menampilkan ${visibleCount} dari ${cards.length} data`;
      }

      // Update empty search state
      if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
      }
    }

    // Event input pencarian
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      applyFilterAndSearch();
    });

    // Event filter kategori (Semua, Guru, Staf)
    filterButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        filterButtons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.getAttribute('data-filter') || 'Semua';
        applyFilterAndSearch();
      });
    });

    // Reset tombol filter jika ada di empty state
    const btnResetSearch = document.getElementById('btn-reset-search');
    if (btnResetSearch) {
      btnResetSearch.addEventListener('click', () => {
        searchInput.value = '';
        searchQuery = '';
        activeFilter = 'Semua';
        filterButtons.forEach((b) => {
          b.classList.toggle('active', b.getAttribute('data-filter') === 'Semua');
        });
        applyFilterAndSearch();
      });
    }
  }

  /* ==========================================================================
     3. FITUR FORM TAMBAH GURU/STAF & FETCH POST SIMPAN
     ========================================================================== */

  function initFormFeatures() {
    const form = document.getElementById('form-tambah-guru');
    if (!form) return;

    const inputNama = document.getElementById('nama');
    const inputJabatan = document.getElementById('jabatan');
    const inputJenis = document.getElementById('jenis');
    const inputEmail = document.getElementById('email');
    const btnSubmit = document.getElementById('btn-submit-form');
    const formAlert = document.getElementById('form-alert-container');

    // Live validation saat user mengetik / blur
    if (inputNama) {
      inputNama.addEventListener('input', () => validateNama(inputNama));
      inputNama.addEventListener('blur', () => validateNama(inputNama));
    }
    if (inputJabatan) {
      inputJabatan.addEventListener('input', () => validateJabatan(inputJabatan));
      inputJabatan.addEventListener('blur', () => validateJabatan(inputJabatan));
    }
    if (inputJenis) {
      inputJenis.addEventListener('change', () => validateJenis(inputJenis));
    }
    if (inputEmail) {
      inputEmail.addEventListener('input', () => validateEmail(inputEmail));
      inputEmail.addEventListener('blur', () => validateEmail(inputEmail));
    }

    // Handle submit form via Fetch POST
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Bersihkan alert lama
      if (formAlert) formAlert.innerHTML = '';

      // Validasi seluruh field client-side
      const isNamaValid = validateNama(inputNama);
      const isJabatanValid = validateJabatan(inputJabatan);
      const isJenisValid = validateJenis(inputJenis);
      const isEmailValid = validateEmail(inputEmail);

      if (!isNamaValid || !isJabatanValid || !isJenisValid || !isEmailValid) {
        showFormAlert('danger', 'Harap perbaiki isian form yang ditandai merah sebelum menyimpan.');
        return;
      }

      // Siapkan payload form
      const formData = new FormData(form);

      // Loading state tombol submit
      setSubmitLoading(btnSubmit, true);

      try {
        const response = await fetch('simpan-guru.php', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (response.ok && result.success) {
          // Sukses simpan
          form.reset();
          clearValidationStates(form);

          showFormAlert(
            'success',
            result.message || 'Data guru/staf berhasil disimpan ke database.',
            true // Tampilkan tombol langsung ke direktori
          );
        } else {
          // Gagal validasi server atau error DB
          const errMsg = result.message || 'Terjadi kesalahan saat memproses data.';
          showFormAlert('danger', errMsg);

          // Tampilkan pesan error spesifik jika server mengembalikan rincian
          if (result.errors && typeof result.errors === 'object') {
            for (const [field, fieldMsg] of Object.entries(result.errors)) {
              setFieldError(field, fieldMsg);
            }
          }
        }
      } catch (err) {
        console.error('[Form Submit Error]', err);
        showFormAlert('danger', 'Terjadi kesalahan jaringan atau server tidak merespon: ' + err.message);
      } finally {
        setSubmitLoading(btnSubmit, false);
      }
    });

    // Reset button handler
    form.addEventListener('reset', () => {
      setTimeout(() => {
        clearValidationStates(form);
        if (formAlert) formAlert.innerHTML = '';
      }, 10);
    });
  }

  /* --- Helper Validasi Form --- */

  function validateNama(input) {
    if (!input) return false;
    const val = input.value.trim();
    if (val.length === 0) {
      setFieldError('nama', 'Nama lengkap wajib diisi.');
      return false;
    }
    if (val.length < 3) {
      setFieldError('nama', 'Nama minimal terdiri dari 3 karakter.');
      return false;
    }
    clearFieldError('nama');
    return true;
  }

  function validateJabatan(input) {
    if (!input) return false;
    const val = input.value.trim();
    if (val.length === 0) {
      setFieldError('jabatan', 'Jabatan / Posisi wajib diisi.');
      return false;
    }
    clearFieldError('jabatan');
    return true;
  }

  function validateJenis(input) {
    if (!input) return false;
    const val = input.value;
    if (!val || (val !== 'Guru' && val !== 'Staf')) {
      setFieldError('jenis', 'Pilih jenis profesi (Guru atau Staf).');
      return false;
    }
    clearFieldError('jenis');
    return true;
  }

  function validateEmail(input) {
    if (!input) return true;
    const val = input.value.trim();
    if (val === '') {
      clearFieldError('email');
      return true; // Opsional
    }
    // RFC 5322 regex sederhana untuk validasi client-side
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(val)) {
      setFieldError('email', 'Format alamat email tidak valid (contoh: nama@domain.com).');
      return false;
    }
    clearFieldError('email');
    return true;
  }

  function setFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    const errorEl = document.getElementById(`error-${fieldName}`);
    if (field) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
    }
    if (errorEl) {
      errorEl.textContent = message;
    }
  }

  function clearFieldError(fieldName) {
    const field = document.getElementById(fieldName);
    const errorEl = document.getElementById(`error-${fieldName}`);
    if (field) {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
    }
    if (errorEl) {
      errorEl.textContent = '';
    }
  }

  function clearValidationStates(form) {
    form.querySelectorAll('.form-control').forEach((el) => {
      el.classList.remove('is-invalid', 'is-valid');
    });
    form.querySelectorAll('.field-error').forEach((el) => {
      el.textContent = '';
    });
  }

  function setSubmitLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
      btn.disabled = true;
      btn.setAttribute('data-original-text', btn.innerHTML);
      btn.innerHTML = `
        <div class="spinner spinner-sm" style="border-top-color:#ffffff; margin-right:6px;"></div>
        Menyimpan Data...
      `;
    } else {
      btn.disabled = false;
      const originalText = btn.getAttribute('data-original-text');
      if (originalText) btn.innerHTML = originalText;
    }
  }

  function showFormAlert(type, message, showViewDirectoryBtn = false) {
    const alertBox = document.getElementById('form-alert-container');
    if (!alertBox) return;

    let iconSvg = '';
    if (type === 'success') {
      iconSvg = `
        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2" stroke-linecap="round"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke-width="2" stroke-linecap="round"/>
        </svg>
      `;
    } else {
      iconSvg = `
        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
        </svg>
      `;
    }

    let extraAction = '';
    if (showViewDirectoryBtn) {
      extraAction = `
        <div class="alert-actions">
          <button type="button" class="btn btn-sm btn-primary" data-view="guru-staf">
            Lihat di Direktori Guru & Staf &rarr;
          </button>
        </div>
      `;
    }

    alertBox.innerHTML = `
      <div class="alert alert-${type}" role="alert">
        ${iconSvg}
        <div class="alert-content">
          <div>${escapeHtml(message)}</div>
          ${extraAction}
        </div>
      </div>
    `;

    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

})();
