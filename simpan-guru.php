<?php
/**
 * Direktori Guru dan Staf SD Islam Al-Hikmah
 * File: simpan-guru.php
 * 
 * Endpoint pemrosesan simpan data guru/staf baru via HTTP POST.
 * Memvalidasi ulang seluruh data di sisi server (Server-Side Validation),
 * mengeksekusi penyimpanan menggunakan PDO Prepared Statements untuk
 * mencegah SQL Injection, dan mengembalikan respon terstruktur (JSON).
 */

declare(strict_types=1);

// Set header JSON untuk seluruh response
header('Content-Type: application/json; charset=UTF-8');

// Hanya izinkan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metode HTTP tidak diizinkan. Hanya menerima request POST.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Hubungkan koneksi database
require_once __DIR__ . DIRECTORY_SEPARATOR . 'koneksi.php';

// Ambil input dari POST (mendukung form-data, urlencoded, maupun raw JSON)
$inputData = $_POST;
$rawPayload = file_get_contents('php://input');
if (empty($inputData) && !empty($rawPayload)) {
    $decoded = json_decode($rawPayload, true);
    if (is_array($decoded)) {
        $inputData = $decoded;
    }
}

// 1. Ekstraksi dan sanitasi awal string
$nama    = isset($inputData['nama']) ? trim((string)$inputData['nama']) : '';
$jabatan = isset($inputData['jabatan']) ? trim((string)$inputData['jabatan']) : '';
$jenis   = isset($inputData['jenis']) ? trim((string)$inputData['jenis']) : '';
$email   = isset($inputData['email']) ? trim((string)$inputData['email']) : '';
$foto    = isset($inputData['foto']) ? trim((string)$inputData['foto']) : '';

// 2. Server-side Validation
$errors = [];

// Validasi Nama
if ($nama === '') {
    $errors['nama'] = 'Nama lengkap wajib diisi.';
} elseif (mb_strlen($nama, 'UTF-8') < 3) {
    $errors['nama'] = 'Nama lengkap minimal 3 karakter.';
} elseif (mb_strlen($nama, 'UTF-8') > 100) {
    $errors['nama'] = 'Nama lengkap maksimal 100 karakter.';
}

// Validasi Jabatan
if ($jabatan === '') {
    $errors['jabatan'] = 'Jabatan / penugasan wajib diisi.';
} elseif (mb_strlen($jabatan, 'UTF-8') < 2) {
    $errors['jabatan'] = 'Jabatan minimal 2 karakter.';
} elseif (mb_strlen($jabatan, 'UTF-8') > 100) {
    $errors['jabatan'] = 'Jabatan maksimal 100 karakter.';
}

// Validasi Jenis (Hanya 'Guru' atau 'Staf')
$allowedJenis = ['Guru', 'Staf'];
if ($jenis === '' || !in_array($jenis, $allowedJenis, true)) {
    $errors['jenis'] = 'Jenis pegawai harus dipilih: Guru atau Staf.';
}

// Validasi Email (Opsional, tapi jika diisi harus valid)
if ($email !== '') {
    if (mb_strlen($email, 'UTF-8') > 150) {
        $errors['email'] = 'Email maksimal 150 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format alamat email tidak valid.';
    }
} else {
    $email = null; // Set NULL di database jika tidak diisi
}

// Validasi & Sanitasi Foto Preset
$allowedAvatars = [
    'avatar-guru-pria.svg',
    'avatar-guru-wanita.svg',
    'avatar-staf-pria.svg',
    'avatar-staf-wanita.svg'
];

if ($foto === '' || !in_array($foto, $allowedAvatars, true)) {
    // Tetapkan default berdasarkan jenis pegawai
    $foto = ($jenis === 'Staf') ? 'avatar-staf-pria.svg' : 'avatar-guru-pria.svg';
}

// Jika terdapat error validasi, kirim HTTP 422 Unprocessable Entity
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Validasi data gagal. Silakan periksa kembali isian Anda.',
        'errors'  => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Eksekusi Penyimpanan ke Database dengan PDO Prepared Statements
try {
    $sql = "INSERT INTO guru_staf (nama, jabatan, jenis, email, foto, created_at)
            VALUES (:nama, :jabatan, :jenis, :email, :foto, NOW())";
    
    $stmt = $pdo->prepare($sql);

    // Bind parameters secara eksplisit
    $stmt->bindValue(':nama', $nama, PDO::PARAM_STR);
    $stmt->bindValue(':jabatan', $jabatan, PDO::PARAM_STR);
    $stmt->bindValue(':jenis', $jenis, PDO::PARAM_STR);
    
    if ($email === null) {
        $stmt->bindValue(':email', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    }

    $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);

    $executeResult = $stmt->execute();

    if ($executeResult) {
        $newId = (int)$pdo->lastInsertId();
        
        http_response_code(201); // 201 Created
        echo json_encode([
            'success' => true,
            'message' => "Data {$jenis} '{$nama}' berhasil disimpan ke direktori.",
            'data'    => [
                'id'         => $newId,
                'nama'       => $nama,
                'jabatan'    => $jabatan,
                'jenis'      => $jenis,
                'email'      => $email,
                'foto'       => $foto
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        throw new PDOException('Gagal menjalankan perintah INSERT pada tabel guru_staf.');
    }

} catch (PDOException $e) {
    // Catat log detail server internal
    error_log('[SIMPAN_GURU_STAF_ERROR] ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan basis data saat menyimpan data pegawai. Harap hubungi administrator atau pastikan database telah diimpor.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
