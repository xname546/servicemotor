========================================================================
SISTEM PENCATATAN SERVIS DAN PENGELOLAAN KOMPONEN KENDARAAN (PHP & MySQL)
========================================================================

Panduan ini menjelaskan cara membuat database dan tabel melalui terminal
(migrasi) serta langkah-langkah memulai sistem dari awal jika database
belum ada.


1. PERSIAPAN AWAL (LARAGON)
------------------------------------------------------------------------
Pastikan aplikasi Laragon sudah dibuka dan layanan berikut sudah aktif
(klik tombol "Start All" di Laragon):
- Apache / Nginx (Web Server)
- MySQL (Database Server, default port: 3306, user: root, password: kosong)


2. PERINTAH TERMINAL UNTUK MIGRASI DATABASE (JIKA DATABASE BELUM ADA)
------------------------------------------------------------------------
Anda TIDAK perlu membuat database secara manual di phpMyAdmin/HeidiSQL.
Cukup jalankan 1 perintah migrasi di terminal, maka script akan otomatis
membuat database `tryone_db` beserta ke-5 tabel yang dibutuhkan.

Langkah-langkah:
1. Buka Terminal (PowerShell / CMD / Terminal Laragon).
2. Pastikan posisi direktori berada di folder project:
   cd C:\laragon\www\tryone

3. Jalankan perintah migrasi berikut:
   php database/migrate.php

Output jika berhasil:
   Menghubungkan ke MySQL Server (127.0.0.1:3306)...
   [OK] Database `tryone_db` siap digunakan.
   [OK] Tabel lama dibersihkan (fresh migrate).
   [OK] Berhasil mengeksekusi: kendaraan.sql
   [OK] Berhasil mengeksekusi: komponen.sql
   [OK] Berhasil mengeksekusi: kendaraan_komponen.sql
   [OK] Berhasil mengeksekusi: service.sql
   [OK] Berhasil mengeksekusi: service_komponen.sql
   Selesai! Seluruh tabel berhasil dimigrasi ulang di database `tryone_db`.

Catatan:
- Perintah `php database/migrate.php` bersifat "Fresh Migrate" (seperti
  `migrate:fresh`), sehingga jika dijalankan ulang akan membersihkan isi
  tabel lama dan membuat ulang struktur tabel paling baru.
- Jika Anda menggunakan username/password MySQL yang berbeda dari bawaan
  Laragon, sesuaikan variabel `$host`, `$port`, `$user`, `$pass`, dan
  `$dbName` pada dua file berikut:
  a. database/migrate.php
  b. database/connection.php


3. CARA MEMBUKA APLIKASI DI BROWSER
------------------------------------------------------------------------
Pilih salah satu cara berikut untuk membuka aplikasi:

Cara A - Menggunakan Apache Laragon:
- Buka browser dan akses:
  http://localhost/tryone
  atau (jika Virtual Host Laragon aktif):
  http://tryone.test

Cara B - Menggunakan PHP Built-in Server via Terminal:
- Jalankan perintah berikut di terminal:
  php -S localhost:8000
- Lalu buka browser dan akses:
  http://localhost:8000


4. ALUR MEMULAI SISTEM PERTAMA KALI (SETELAH MIGRASI DATABASE BARU)
------------------------------------------------------------------------
Saat database baru saja dibuat, tabel `Kendaraan` dan `Komponen` masih
kosong. Ikuti urutan berikut untuk mulai menggunakan sistem:

LANGKAH 1: Masuk sebagai Admin
1. Pada halaman awal (Pilih Akun Masuk), pilih:
   "Admin (Kelola Kendaraan & Master Komponen)"
2. Klik tombol "Masuk ke Dashboard".

LANGKAH 2: Tambahkan Master Komponen & Kendaraan (Di Panel Admin)
1. Buka menu "Master Komponen" -> klik "+ Tambah Master Komponen".
   Isi Kode Komponen, Nama Komponen, Kategori Kendaraan (misal: Motor Matic),
   serta Interval Cek KM dan/atau Interval Ganti KM, lalu simpan.
2. Buka menu "Daftar Kendaraan" -> klik "+ Tambah Kendaraan Baru".
   Isi Nama Kendaraan, Kategori Jenis Kendaraan, Nomor Polisi, dan
   KM Odometer Awal, lalu simpan.
3. Klik tombol "Ganti Akun / Keluar" di bagian kiri bawah sidebar.

LANGKAH 3: Masuk sebagai Akun Kendaraan
1. Pada halaman awal (Pilih Akun Masuk), pilih kendaraan yang baru saja
   Anda daftarkan pada bagian "Akun Kendaraan".
2. Klik "Masuk ke Dashboard".
3. Sekarang Anda dapat:
   - Memasukkan komponen yang ingin dipantau pada menu "Komponen Kendaraan".
   - Menginput odometer terbaru pada menu "Beranda & Cek KM" untuk melihat
     status warna komponen (Harus Diganti / Perlu Dicek / Aman).
   - Mencatat riwayat kunjungan servis, upload foto nota, serta mencatat
     komponen yang Diganti atau Dicek pada menu "Riwayat Servis".
========================================================================
