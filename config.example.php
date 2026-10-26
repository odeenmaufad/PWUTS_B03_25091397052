<?php
/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: config.example.php
 * 
 * CATATAN PENGGUNAAN:
 * Salin file ini menjadi 'config.local.php' pada komputer lokal Anda jika ingin
 * mengubah pengaturan database default. File 'config.local.php' sudah diabaikan
 * oleh Git (.gitignore) sehingga kredensial aman dari repositori publik.
 */

return [
    'db_host'    => '127.0.0.1',
    'db_port'    => '3306',
    'db_name'    => 'db_tugas_guru_staf',
    'db_user'    => 'root',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',
    // Opsi PDO tambahan
    'pdo_options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
