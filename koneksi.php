<?php
/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: koneksi.php
 * 
 * Pengelola koneksi database menggunakan PDO (PHP Data Objects).
 * Mendukung konfigurasi kustom via config.local.php dengan fallback otomatis
 * ke kredensial standar XAMPP (root tanpa password).
 */

declare(strict_types=1);

function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // Default configuration (standar XAMPP)
    $config = [
        'db_host'    => '127.0.0.1',
        'db_port'    => '3306',
        'db_name'    => 'db_tugas_guru_staf',
        'db_user'    => 'root',
        'db_pass'    => '',
        'db_charset' => 'utf8mb4',
        'pdo_options' => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    ];

    // Cek keberadaan file config.local.php
    $localConfigFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.local.php';
    if (file_exists($localConfigFile) && is_readable($localConfigFile)) {
        $localConfig = require $localConfigFile;
        if (is_array($localConfig)) {
            $config = array_replace_recursive($config, $localConfig);
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['db_host'],
        $config['db_port'],
        $config['db_name'],
        $config['db_charset']
    );

    try {
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $config['pdo_options']);
        return $pdo;
    } catch (PDOException $e) {
        // Catat detail teknis ke error log server tanpa membocorkan kredensial ke publik
        error_log('[DB_CONNECTION_ERROR] ' . $e->getMessage());

        // Jika request berupa AJAX / Fetch API (HTTP_X_REQUESTED_WITH atau Accept application/json)
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isAjax) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => false,
                'message' => 'Gagal terhubung ke database. Pastikan MySQL di XAMPP aktif dan database "db_tugas_guru_staf" sudah diimpor.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Tampilan pesan ramah untuk request HTML
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Koneksi Database Bermasalah - SD Islam Al-Hikmah</title>
            <style>
                body {
                    font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
                    background: #f8fafc;
                    color: #1e293b;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 1.5rem;
                    box-sizing: border-box;
                }
                .error-card {
                    background: #ffffff;
                    border: 1px solid #fee2e2;
                    border-top: 4px solid #dc2626;
                    border-radius: 12px;
                    padding: 2rem;
                    max-width: 540px;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
                }
                h1 { font-size: 1.25rem; color: #991b1b; margin-top: 0; }
                p { line-height: 1.6; color: #475569; font-size: 0.95rem; }
                ol { padding-left: 1.2rem; line-height: 1.6; color: #334155; }
                code { background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.9em; }
                .btn {
                    display: inline-block;
                    margin-top: 1rem;
                    padding: 0.6rem 1.2rem;
                    background: #1b6e47;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="error-card">
                <h1>Koneksi Database Belum Terhubung</h1>
                <p>Aplikasi tidak dapat menghubungi basis data MariaDB/MySQL. Harap pastikan langkah berikut sudah dilakukan:</p>
                <ol>
                    <li>Modul <strong>MySQL</strong> pada XAMPP Control Panel sudah dalam status <strong>Running</strong>.</li>
                    <li>Database <code>db_tugas_guru_staf</code> telah diimpor menggunakan file <code>database.sql</code>.</li>
                    <li>Jika menggunakan password database non-default, sesuaikan di file <code>config.local.php</code>.</li>
                </ol>
                <a href="index.html" class="btn">Muat Ulang Halaman</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Inisialisasi variabel global PDO untuk kemudahan inklusi
$pdo = getDbConnection();
