# Direktori Guru dan Staf SD Islam Al-Hikmah
### Tugas Pemrograman Web Lanjut

- **Nama Mahasiswa:** Muhammad Aufa D
- **NIM:** 25091397052
- **Kelompok:** B03
- **Mata Kuliah:** Pemrograman Web Lanjut

**Mohon Izin Sebelumnya. Readme ini Saya meminta bantuan Ai untuk membantu saya Menyusun Penulisannya Agar memudahkan Saya Menjelaskan Program Saya**
---

## 1. Deskripsi Proyek

Website **Direktori Guru dan Staf SD Islam Al-Hikmah** adalah aplikasi berbasis web yang berfungsi sebagai pusat informasi profil tenaga pendidik (guru) dan tenaga kependidikan (staf) di lingkungan sekolah dasar Islam Al-Hikmah. 

Tujuan pembuatan website ini adalah untuk menyajikan data dewan guru dan staf sekolah secara rapi, mudah dicari, dan responsif, sekaligus memenuhi tugas mata kuliah Pemrograman Web Lanjut dengan menerapkan konsep navigasi asinkron (AJAX), validasi formulir interaktif di sisi klien dan server, serta pengelolaan basis data menggunakan PHP Data Objects (PDO).

---

## 2. Fitur Utama

Fitur-fitur yang sudah diimplementasikan dan dapat diuji pada aplikasi ini meliputi:

1. **Halaman Beranda (Homepage):** Menampilkan identitas sekolah, lambang resmi, ilustrasi kegiatan belajar, statistik ringkas, dan rangkuman keunggulan website.
2. **Direktori Guru & Staf:** Menampilkan daftar profil pegawai dalam bentuk kartu (*cards*) yang memuat foto avatar, nama lengkap, jabatan, jenis profesi (Guru atau Staf), dan alamat email yang diambil langsung dari database MySQL.
3. **Pencarian Real-Time & Filter Kategori:** Fitur pencarian instan berdasarkan nama atau jabatan guru/staf secara langsung saat mengetik (*live search*), tombol filter kategori profesi (*Semua*, *Dewan Guru*, *Staf*), penghitung jumlah data, serta tampilan khusus bila hasil pencarian tidak ditemukan.
4. **Formulir Tambah Pegawai Baru:** Formulir pendaftaran guru/staf baru yang dilengkapi pilihan avatar ilustrasi lokal, validasi input sebelum pengiriman, pesan error di dekat kolom input, dan notifikasi setelah data tersimpan.
5. **Navigasi Single-Page (AJAX):** Perpindahan antar halaman konten dilakukan secara asinkron tanpa *reload* seluruh halaman, lengkap dengan animasi *loading*, penanganan pesan kesalahan, serta dukungan tombol *Back* dan *Forward* pada browser.

> *Catatan:* Sesuai cakupan tugas, aplikasi ini berfokus pada penampilan direktori dan penambahan data baru (belum mencakup fitur ubah atau hapus data).

---

## 3. Teknologi yang Digunakan

- **HTML5:** Struktur semantik halaman (`<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`).
- **CSS3 (Vanilla):** Pengaturan tata letak responsif (*Flexbox* dan *CSS Grid*), variabel warna tema hijau Islami, dan animasi transisi tanpa menggunakan *framework* eksternal seperti Bootstrap atau Tailwind.
- **JavaScript (Vanilla ES6+):** Logika antarmuka klien menggunakan *Fetch API* untuk AJAX, manipulasi DOM, pemfilteran data pencarian, serta validasi formulir.
- **PHP 8:** Pemrosesan logika di sisi server, validasi ulang data masukan, dan penyedia data fragmen HTML/JSON.
- **Basis Data MariaDB/MySQL:** Penyimpanan data relasional menggunakan koneksi **PDO (PHP Data Objects)** dengan fitur *Prepared Statements*.
- **Aset Gambar Lokal:** Menggunakan gambar lambang resmi (`Al-HikmahIslamicSchoolEmblem.png`) dan ilustrasi vektor SVG lokal tanpa dependensi internet/CDN.

---

## 4. Pemenuhan Persyaratan Tugas

- **Tugas 2.a (Homepage Responsif & Navigasi AJAX):**  
  Homepage utama diberi nama `index.html` dan dirancang responsif untuk perangkat komputer, tablet, maupun ponsel pintar. Navigasi menuju halaman Direktori (`guru-staf.php`) dan Tambah Pegawai (`tambah-guru.php`) menggunakan Fetch API AJAX, sehingga konten langsung dimuat ke dalam area utama (`#main-content`) tanpa memuat ulang seluruh halaman.

- **Tugas 2.b (Konten Interaktif pada Layout Utama):**  
  Halaman `guru-staf.php` dimuat ke dalam layout utama dan mengambil data riil dari tabel `guru_staf`. Di dalamnya terdapat konten interaktif berupa pencarian langsung nama/jabatan serta tombol filter kategori pegawai. Seluruh data keluaran disanitasi menggunakan `htmlspecialchars` untuk mencegah celah keamanan XSS.

- **Tugas 2.c (Form Transaksi dengan Validasi JavaScript & Database):**  
  Halaman `tambah-guru.php` berisi formulir penambahan data yang sesuai dengan struktur kolom tabel `guru_staf`. Formulir memiliki validasi JavaScript sebelum data dikirim (nama minimal 3 huruf, jabatan wajib diisi, jenis pegawai wajib dipilih, dan format email valid bila diisi). Data kemudian dikirim via AJAX POST ke `simpan-guru.php`, divalidasi ulang di sisi server, dan disimpan ke database menggunakan *PDO Prepared Statements*.

---

## 5. Cara Menjalankan Website di XAMPP

Berikut langkah-langkah untuk menjalankan website ini pada komputer lokal:

### Langkah 1: Penempatan Folder Proyek
Pastikan seluruh berkas proyek berada di dalam folder `htdocs` XAMPP Anda, contoh:
```text
C:\xampp\htdocs\TugasWeb_GuruStaf\
```

### Langkah 2: Menjalankan Apache dan MySQL
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada baris **Apache**.
3. Klik tombol **Start** pada baris **MySQL**.
4. Pastikan kedua modul tersebut sudah berwarna hijau (*status: Running*).

### Langkah 3: Mengimpor Database melalui phpMyAdmin
1. Buka peramban web dan akses alamat: `http://localhost/phpmyadmin/`.
2. Klik menu **Import** pada bagian atas halaman.
3. Klik tombol **Choose File** (Pilih File) dan arahkan ke file `database.sql` yang ada di dalam folder proyek:
   ```text
   C:\xampp\htdocs\TugasWeb_GuruStaf\database.sql
   ```
4. Gulir ke bawah dan klik tombol **Import** (atau **Go**).
5. Database `db_tugas_guru_staf` beserta tabel `guru_staf` dan data awal akan otomatis terbuat.

### Langkah 4: Konfigurasi Database (Bila Diperlukan)
Secara bawaan, aplikasi disetel untuk konfigurasi standar XAMPP (host: `127.0.0.1`, port: `3306`, user: `root`, password: kosong `""`).  
Jika MySQL di komputer Anda menggunakan password khusus, buat salinan file `config.example.php` menjadi `config.local.php` lalu sesuaikan password Anda di sana. File `config.local.php` sudah diatur agar tidak ikut terunggah ke Git.

### Langkah 5: Membuka Website
Buka browser dan ketikkan alamat berikut:
```text
http://localhost/TugasWeb_GuruStaf/
```

---

## 6. Catatan Pengujian yang Telah Dilakukan

Pengujian fungsionalitas berikut telah dilakukan pada peramban web:

1. **Uji Navigasi AJAX:** Berpindah dari Beranda ke Direktori Guru & Staf dan Tambah Data berhasil memuat konten secara langsung tanpa reload halaman. Animasi *loading* muncul dengan baik saat proses pengambilan data.
2. **Uji Riwayat Browser:** Menekan tombol *Back* dan *Forward* pada peramban web berfungsi normal untuk berpindah antar halaman yang baru saja dibuka.
3. **Uji Pencarian & Filter:** Mengetik nama guru (misalnya "Ahmad" atau "Siti") langsung menyaring kartu yang sesuai di layar. Memilih filter "Dewan Guru" atau "Staf" juga berhasil memisahkan data berdasarkan profesi.
4. **Uji Validasi Formulir:** Mencoba mengirim formulir kosong atau mengisi nama kurang dari 3 huruf langsung menampilkan pesan peringatan merah di bawah kolom terkait dan menahan pengiriman data.
5. **Uji Penyimpanan Data Baru:** Memasukkan data baru yang valid berhasil tersimpan ke database MySQL melalui mekanisme *Prepared Statements*, formulir di-reset, notifikasi sukses muncul, dan data baru langsung tampil saat membuka halaman Direktori.
6. **Data Awal Aplikasi:** Seluruh data awal guru dan staf pada file `database.sql` merupakan data contoh (*dummy data*) yang digunakan khusus untuk keperluan pengujian dan peragaan tugas kuliah.
