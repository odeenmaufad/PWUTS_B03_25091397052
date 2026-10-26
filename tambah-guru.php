<?php
/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: tambah-guru.php
 * 
 * Menghasilkan fragmen HTML formulir pendaftaran Guru / Staf baru.
 * Dimuat secara dinamis via Fetch API (Ajax) oleh index.html.
 * Dilengkapi validasi client-side, pesan error spesifik, dan avatar preset lokal.
 */

declare(strict_types=1);

// Deteksi apakah request berasal dari Fetch API / AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
       || (isset($_GET['fragment']) && $_GET['fragment'] === '1');

// Jika diakses langsung tanpa AJAX, sertakan wrapper halaman lengkap
if (!$isAjax): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Guru & Staf - SD Islam Al-Hikmah</title>
    <link rel="icon" type="image/png" href="assets/img/Al-HikmahIslamicSchoolEmblem.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container nav-container">
            <a href="index.html" class="brand">
                <img src="assets/img/Al-HikmahIslamicSchoolEmblem.png" alt="Logo SD Islam Al-Hikmah" class="brand-logo" width="46" height="46">
                <div class="brand-text">
                    <span class="brand-title">SD Islam Al-Hikmah</span>
                    <span class="brand-subtitle">Direktori Tenaga Pendidik & Kependidikan</span>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.html" class="nav-link" data-view="beranda">Beranda</a></li>
                    <li><a href="guru-staf.php" class="nav-link" data-view="guru-staf">Direktori Guru & Staf</a></li>
                    <li><a href="tambah-guru.php" class="nav-link active" data-view="tambah-guru">Tambah Guru/Staf</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main id="main-content">
<?php endif; ?>

<!-- =========================================================================
     FRAGMEN HTML FORM TAMBAH GURU/STAF (Dimuat oleh index.html via Ajax)
     ========================================================================= -->
<div class="container view-container" id="view-tambah-content">

    <!-- Header Halaman Form -->
    <div class="view-header">
        <div class="view-title-group">
            <h1>Tambah Data Guru & Staf Baru</h1>
            <p>Masukkan identitas tenaga pendidik atau kependidikan baru ke dalam direktori sekolah</p>
        </div>
        <div>
            <a href="guru-staf.php" data-view="guru-staf" class="btn btn-secondary btn-sm">
                &larr; Lihat Direktori
            </a>
        </div>
    </div>

    <!-- Kontainer Pesan Feedback Sukses / Gagal Form -->
    <div id="form-alert-container" aria-live="polite"></div>

    <!-- Kartu Formulir Pendaftaran -->
    <div class="form-card">
        <div class="form-header-note">
            <strong>Petunjuk Pengisian:</strong> Isikan data dengan benar. Tanda bintang (<span style="color:#b91c1c;">*</span>) menandakan kolom wajib diisi. Data akan divalidasi langsung oleh sistem di peramban dan server secara aman.
        </div>

        <form id="form-tambah-guru" method="POST" action="simpan-guru.php" novalidate>
            <div class="form-grid">
                
                <!-- Field: Nama Lengkap -->
                <div class="form-group form-col-full">
                    <label for="nama" class="form-label">
                        <span>Nama Lengkap Beserta Gelar <span class="req">*</span></span>
                        <span class="hint">Minimal 3 karakter</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        class="form-control" 
                        placeholder="Contoh: Ustadz H. Ahmad Fauzi, S.Pd.I" 
                        required 
                        minlength="3"
                        maxlength="100"
                        autocomplete="name"
                    >
                    <div id="error-nama" class="field-error"></div>
                </div>

                <!-- Field: Jabatan / Tugas -->
                <div class="form-group">
                    <label for="jabatan" class="form-label">
                        <span>Jabatan / Penugasan <span class="req">*</span></span>
                        <span class="hint">Wajib diisi</span>
                    </label>
                    <input 
                        type="text" 
                        id="jabatan" 
                        name="jabatan" 
                        class="form-control" 
                        placeholder="Contoh: Guru Tematik Kelas 4 / IT Support" 
                        required 
                        maxlength="100"
                    >
                    <div id="error-jabatan" class="field-error"></div>
                </div>

                <!-- Field: Jenis Profesi (Guru / Staf) -->
                <div class="form-group">
                    <label for="jenis" class="form-label">
                        <span>Jenis Pegawai <span class="req">*</span></span>
                        <span class="hint">Pilih profesi</span>
                    </label>
                    <select id="jenis" name="jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis Pegawai --</option>
                        <option value="Guru">Guru (Tenaga Pendidik)</option>
                        <option value="Staf">Staf (Kependidikan / Administrasi)</option>
                    </select>
                    <div id="error-jenis" class="field-error"></div>
                </div>

                <!-- Field: Email (Opsional) -->
                <div class="form-group form-col-full">
                    <label for="email" class="form-label">
                        <span>Alamat Email</span>
                        <span class="hint">Opsional (bila ada, harus valid)</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Contoh: ahmad.fauzi@alhikmah.sch.id" 
                        maxlength="150"
                        autocomplete="email"
                    >
                    <div id="error-email" class="field-error"></div>
                </div>

                <!-- Field: Pilihan Avatar / Foto Profil Lokal -->
                <div class="form-group form-col-full">
                    <label class="form-label">
                        <span>Pilihan Avatar Ilustrasi Lokal</span>
                        <span class="hint">Gunakan ilustrasi SVG yang tersedia</span>
                    </label>
                    <div class="avatar-preset-grid">
                        <label class="avatar-radio-item">
                            <input type="radio" name="foto" value="avatar-guru-pria.svg" checked>
                            <div class="avatar-radio-card">
                                <img src="assets/img/avatar-guru-pria.svg" alt="Preset Guru Pria">
                                <span class="avatar-radio-label">Guru Pria</span>
                            </div>
                        </label>
                        <label class="avatar-radio-item">
                            <input type="radio" name="foto" value="avatar-guru-wanita.svg">
                            <div class="avatar-radio-card">
                                <img src="assets/img/avatar-guru-wanita.svg" alt="Preset Guru Wanita">
                                <span class="avatar-radio-label">Guru Wanita</span>
                            </div>
                        </label>
                        <label class="avatar-radio-item">
                            <input type="radio" name="foto" value="avatar-staf-pria.svg">
                            <div class="avatar-radio-card">
                                <img src="assets/img/avatar-staf-pria.svg" alt="Preset Staf Pria">
                                <span class="avatar-radio-label">Staf Pria</span>
                            </div>
                        </label>
                        <label class="avatar-radio-item">
                            <input type="radio" name="foto" value="avatar-staf-wanita.svg">
                            <div class="avatar-radio-card">
                                <img src="assets/img/avatar-staf-wanita.svg" alt="Preset Staf Wanita">
                                <span class="avatar-radio-label">Staf Wanita</span>
                            </div>
                        </label>
                    </div>
                    <div id="error-foto" class="field-error"></div>
                </div>

            </div>

            <!-- Tombol Aksi Formulir -->
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">
                    Reset Form
                </button>
                <button type="submit" id="btn-submit-form" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan Data Pegawai
                </button>
            </div>
        </form>
    </div>

</div>

<?php if (!$isAjax): ?>
    </main>
    <footer class="site-footer">
        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> SD Islam Al-Hikmah - Formulir Tambah Data (Akses Langsung)</p>
            <p><a href="index.html">&larr; Kembali ke Beranda Utama</a></p>
        </div>
    </footer>
    <script src="assets/js/app.js"></script>
</body>
</html>
<?php endif; ?>
