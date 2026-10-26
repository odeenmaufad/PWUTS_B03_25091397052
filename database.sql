-- ============================================================================
-- Direktori Guru dan Staf SD Islam Al-Hikmah
-- File: database.sql
-- Tujuan: Skema Basis Data dan Data Dummy Contoh untuk Tugas Pemrograman Web
-- Catatan: Data yang disertakan murni contoh ilustrasi (dummy) untuk keperluan
--          pengujian aplikasi, bukan data riil tenaga pengajar sebenarnya.
-- ============================================================================

-- 1. Buat Basis Data jika belum tersedia
CREATE DATABASE IF NOT EXISTS `db_tugas_guru_staf`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `db_tugas_guru_staf`;

-- 2. Buat Tabel guru_staf
CREATE TABLE IF NOT EXISTS `guru_staf` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(100) NOT NULL,
    `jabatan` VARCHAR(100) NOT NULL,
    `jenis` ENUM('Guru', 'Staf') NOT NULL,
    `email` VARCHAR(150) NULL DEFAULT NULL,
    `foto` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_jenis` (`jenis`),
    INDEX `idx_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Data Dummy Contoh (Idempoten: Aman diimpor berulang kali tanpa duplikasi)
-- Menggunakan klausa ON DUPLICATE KEY UPDATE dengan ID tetap (1-6)
INSERT INTO `guru_staf` (`id`, `nama`, `jabatan`, `jenis`, `email`, `foto`) VALUES
(
    1,
    'Ustadz H. Muhammad Ridwan, Lc., M.Pd.',
    'Kepala Sekolah & Pembina PAI',
    'Guru',
    'ridwan.dummy@alhikmah.sch.id',
    'avatar-guru-pria.svg'
),
(
    2,
    'Ustadzah Siti Fatimah, S.Pd.',
    'Wali Kelas 1 & Guru Tematik',
    'Guru',
    'siti.dummy@alhikmah.sch.id',
    'avatar-guru-wanita.svg'
),
(
    3,
    'Ustadz Ahmad Fauzan, S.Pd.',
    'Guru PJOK & Pembina Pramuka',
    'Guru',
    'fauzan.dummy@alhikmah.sch.id',
    'avatar-guru-pria.svg'
),
(
    4,
    'Ustadzah Nurul Hidayati, S.Si.',
    'Guru Sains (IPA) & Koordinator Tahfizh',
    'Guru',
    'nurul.dummy@alhikmah.sch.id',
    'avatar-guru-wanita.svg'
),
(
    5,
    'Bapak Bambang Prasetyo, S.Kom.',
    'Kepala Tata Usaha & Administrator IT',
    'Staf',
    'bambang.dummy@alhikmah.sch.id',
    'avatar-staf-pria.svg'
),
(
    6,
    'Ibu Khadijah Wulandari, A.Md.',
    'Staf Keuangan & Pelayanan Siswa',
    'Staf',
    'khadijah.dummy@alhikmah.sch.id',
    'avatar-staf-wanita.svg'
)
ON DUPLICATE KEY UPDATE
    `nama` = VALUES(`nama`),
    `jabatan` = VALUES(`jabatan`),
    `jenis` = VALUES(`jenis`),
    `email` = VALUES(`email`),
    `foto` = VALUES(`foto`);
