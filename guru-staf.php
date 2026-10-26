<?php
/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: guru-staf.php
 * 
 * Menghasilkan fragmen HTML direktori data guru dan staf dari database MariaDB/MySQL.
 * Mendukung pemuatan dinamis via Fetch API (Ajax) maupun akses mandiri (fallback).
 * Semua output di-escape secara ketat menggunakan htmlspecialchars() untuk proteksi XSS.
 */

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'koneksi.php';

// Deteksi apakah request berasal dari Fetch API / AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
       || (isset($_GET['fragment']) && $_GET['fragment'] === '1');

// Ambil data dari tabel guru_staf
$daftarPegawai = [];
$errorMessage = null;

try {
    $sql = "SELECT id, nama, jabatan, jenis, email, foto, created_at 
            FROM guru_staf 
            ORDER BY jenis ASC, nama ASC";
    $stmt = $pdo->query($sql);
    $daftarPegawai = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[FETCH_GURU_STAF_ERROR] ' . $e->getMessage());
    $errorMessage = 'Terjadi kendala saat membaca data dari database. Pastikan tabel "guru_staf" sudah tersedia.';
}

// Helper sanitasi output HTML
function e(?string $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

// Jika diakses langsung tanpa AJAX, sertakan wrapper halaman lengkap agar tetap rapi di browser
if (!$isAjax): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktori Guru & Staf - SD Islam Al-Hikmah</title>
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
                    <li><a href="guru-staf.php" class="nav-link active" data-view="guru-staf">Direktori Guru & Staf</a></li>
                    <li><a href="tambah-guru.php" class="nav-link" data-view="tambah-guru">Tambah Guru/Staf</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main id="main-content">
<?php endif; ?>

<!-- =========================================================================
     FRAGMEN HTML DIREKTORI GURU & STAF (Dimuat oleh index.html via Ajax)
     ========================================================================= -->
<div class="container view-container" id="view-direktori-content">
    
    <!-- Header Halaman Direktori -->
    <div class="view-header">
        <div class="view-title-group">
            <h1>Direktori Guru & Tenaga Kependidikan</h1>
            <p>Daftar resmi dewan guru, ustadz/ustadzah, dan staf administrasi SD Islam Al-Hikmah</p>
        </div>
        <div>
            <a href="tambah-guru.php" data-view="tambah-guru" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Data Pegawai
            </a>
        </div>
    </div>

    <?php if ($errorMessage !== null): ?>
        <div class="alert alert-danger" role="alert">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
                <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
            </svg>
            <div class="alert-content">
                <strong>Peringatan Sistem:</strong> <?= e($errorMessage) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Toolbar Pencarian & Filter Cepat (JavaScript-Powered) -->
    <div class="toolbar-card">
        <div class="search-box-wrapper">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
            </svg>
            <input 
                type="text" 
                id="search-guru" 
                class="search-input" 
                placeholder="Cari berdasarkan nama atau jabatan guru/staf..." 
                aria-label="Pencarian guru dan staf"
                autocomplete="off"
            >
        </div>
        
        <div class="filter-group">
            <button type="button" class="filter-btn active" data-filter="Semua">Semua Pegawai</button>
            <button type="button" class="filter-btn" data-filter="Guru">Dewan Guru</button>
            <button type="button" class="filter-btn" data-filter="Staf">Staf & Administrasi</button>
        </div>

        <div class="stats-counter" id="counter-display">
            Menampilkan <?= count($daftarPegawai) ?> dari <?= count($daftarPegawai) ?> data
        </div>
    </div>

    <!-- Status Hasil Pencarian Kosong (Client-side JS Filter) -->
    <div class="empty-state" id="empty-search-state" style="display: none;">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="1.8"/>
            <line x1="8" y1="11" x2="14" y2="11" stroke-width="1.8"/>
        </svg>
        <h3 class="empty-title">Data Tidak Ditemukan</h3>
        <p class="empty-desc">Tidak ada guru atau staf yang cocok dengan kata kunci pencarian Anda.</p>
        <button type="button" class="btn btn-secondary btn-sm" id="btn-reset-search">Reset Pencarian</button>
    </div>

    <?php if (empty($daftarPegawai)): ?>
        <!-- State jika database benar-benar kosong -->
        <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/>
                <circle cx="9" cy="7" r="4" stroke-width="1.8"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-width="1.8"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/>
            </svg>
            <h3 class="empty-title">Belum Ada Data Guru & Staf</h3>
            <p class="empty-desc">Basis data saat ini belum memiliki rekaman guru atau staf. Anda dapat mengimpor file database.sql atau menambahkan data baru melalui form.</p>
            <a href="tambah-guru.php" data-view="tambah-guru" class="btn btn-primary">
                + Tambah Data Pertama
            </a>
        </div>
    <?php else: ?>
        <!-- Grid Kartu Guru & Staf Responsif -->
        <div class="directory-grid" id="directory-grid">
            <?php foreach ($daftarPegawai as $row): 
                $jenisClass = ($row['jenis'] === 'Staf') ? 'badge-staf' : 'badge-guru';
                
                // Tentukan file avatar fallback berdasarkan jenis & nama file
                $fotoFile = !empty($row['foto']) ? trim($row['foto']) : '';
                $avatarPath = 'assets/img/' . $fotoFile;
                
                // Validasi fisik file avatar lokal
                if (empty($fotoFile) || !file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fotoFile)) {
                    $avatarPath = ($row['jenis'] === 'Staf') 
                        ? 'assets/img/avatar-staf-pria.svg' 
                        : 'assets/img/avatar-guru-pria.svg';
                }
            ?>
                <article 
                    class="person-card" 
                    data-nama="<?= e($row['nama']) ?>" 
                    data-jabatan="<?= e($row['jabatan']) ?>" 
                    data-jenis="<?= e($row['jenis']) ?>"
                >
                    <div class="card-top">
                        <div class="avatar-wrapper">
                            <img 
                                src="<?= e($avatarPath) ?>" 
                                alt="Foto profil <?= e($row['nama']) ?>" 
                                class="avatar-img"
                                loading="lazy"
                            >
                        </div>
                        <span class="badge <?= $jenisClass ?>">
                            <span class="badge-dot"></span>
                            <?= e($row['jenis']) ?>
                        </span>
                    </div>

                    <h2 class="person-nama"><?= e($row['nama']) ?></h2>
                    <p class="person-jabatan"><?= e($row['jabatan']) ?></p>

                    <div class="card-details">
                        <div class="detail-row">
                            <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="4" width="20" height="16" rx="2" stroke-width="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke-width="2"/>
                            </svg>
                            <?php if (!empty($row['email'])): ?>
                                <a href="mailto:<?= e($row['email']) ?>" class="detail-email-link" title="Kirim email ke <?= e($row['nama']) ?>">
                                    <?= e($row['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="detail-empty">Email tidak dicantumkan</span>
                            <?php endif; ?>
                        </div>

                        <div class="detail-row">
                            <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <polyline points="12 6 12 12 16 14" stroke-width="2"/>
                            </svg>
                            <span>
                                Terdaftar: <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php if (!$isAjax): ?>
    </main>
    <footer class="site-footer">
        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> SD Islam Al-Hikmah - Direktori Guru & Staf (Akses Langsung)</p>
            <p><a href="index.html">&larr; Kembali ke Beranda Utama</a></p>
        </div>
    </footer>
    <script src="assets/js/app.js"></script>
</body>
</html>
<?php endif; ?>
