# Views Guide: Spesifikasi Halaman, Komponen UI, dan Tujuan Halaman

Dokumen ini menjelaskan secara rinci seluruh **15 halaman view** yang ada pada sistem (beserta pemetaan nama file antara versi **Google Apps Script `.html`** dan versi **PHP Native `.php`**), tujuan setiap halaman, data yang diterima dari Controller, serta elemen-elemen antarmuka (UI) yang wajib ada di dalamnya.

---

## 1. Standar Antarmuka (UI/UX Guidelines)

1. **Phone-View & Responsive Bootstrap 5**:
   - Menggunakan **Bootstrap 5.3.3** murni.
   - Pada layar HP (`d-md-none`), navigasi utama berada di **Bottom Navigation Bar** (`fixed-bottom`).
   - Pada layar Laptop/Desktop (`d-none d-md-flex`), navigasi berada di **Top Navbar** (`sticky-top`).
2. **Animasi Loading Submit Form**:
   - Seluruh halaman yang memiliki `<form>` dilengkapi *event listener* `submit` yang menonaktifkan tombol submit (`btn.disabled = true`) dan mengubah isi tombol menjadi spinner Bootstrap:
     ```html
     <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...
     ```
3. **Pemisah Teks Bersih**:
   - Menggunakan karakter pemisah standar (`|` atau `-`) agar aman terhadap *auto-escaping* template engine.

---

## 2. Tabel Pemetaan File View (Apps Script $\leftrightarrow$ PHP Native)

| No | Sisi Pengguna | File View Apps Script (`views/*.html`) | File View PHP Native (`views/**/*.php`) | Tujuan Utama Halaman |
|---|---|---|---|---|
| 1 | **Umum** | `AuthLogin.html` | `views/auth/login.php` | Pilih akun masuk (Admin atau salah satu Kendaraan) tanpa password. |
| 2 | **Admin** | `DaftarKendaraan.html` | `views/kendaraan/index.php` | Melihat daftar seluruh kendaraan terdaftar beserta KM aktualnya. |
| 3 | **Admin** | `BuatKendaraan.html` | `views/kendaraan/create.php` | Form pendaftaran akun kendaraan baru (5 pilihan jenis kendaraan). |
| 4 | **Admin** | `DaftarKomponen.html` | `views/komponen/index.php` | Katalog master komponen dengan badge kategori & interval cek/ganti. |
| 5 | **Admin** | `BuatKomponen.html` | `views/komponen/create.php` | Form tambah master komponen baru (kategori + interval cek & ganti). |
| 6 | **Admin** | `UbahKomponen.html` | `views/komponen/edit.php` | Form ubah data master komponen. |
| 7 | **Kendaraan** | `Dashboard.html` | `views/dashboard/index.php` | Beranda kendaraan, input Cek KM baru, & kartu status komponen berurut dari Warning Merah $\rightarrow$ Kuning $\rightarrow$ Aman. |
| 8 | **Kendaraan** | `UbahKendaraan.html` | `views/kendaraan/edit.php` | Form ubah profil kendaraan yang sedang aktif. |
| 9 | **Kendaraan** | `DaftarKomponenKendaraan.html` | `views/kendaraan_komponen/index.php` | Daftar komponen kendaraan, badge jumlah riwayat (`X Riwayat (Yx Ganti, Zx Cek)`), serta tombol menuju detail riwayat. |
| 10 | **Kendaraan** | `BuatKomponenKendaraan.html` | `views/kendaraan_komponen/create.php` | Form mendaftarkan komponen baru ke kendaraan (pilihan sudah tersaring sesuai kategori kendaraan). |
| 11 | **Kendaraan** | `DetailKomponenKendaraan.html` | `views/kendaraan_komponen/show.php` | Info keterangan komponen, rata-rata interval/selisih KM, dan tabel riwayat penggantian/pengecekan komponen. |
| 12 | **Kendaraan** | `DaftarService.html` | `views/service/index.php` | Daftar riwayat servis dengan badge urutan kronologis (`Servis ke-1`..`ke-N`, `Servis Pertama`/`Terakhir`) & tombol lihat Nota. |
| 13 | **Kendaraan** | `BuatService.html` | `views/service/create.php` | Form catat transaksi servis baru (mendukung servis mundur sebelum odometer & upload foto nota). |
| 14 | **Kendaraan** | `UbahService.html` | `views/service/edit.php` | Form ubah data header transaksi servis & ganti foto nota. |
| 15 | **Kendaraan** | `DetailService.html` | `views/service/show.php` | Ringkasan biaya servis, link foto nota, form tambah tindakan komponen (`Ganti`/`Cek`), dan daftar komponen pada servis tersebut. |

*(Catatan untuk versi PHP: 4 file layout `views/layouts/header.php`, `footer.php`, `header_admin.php`, dan `footer_admin.php` tetap dapat digunakan via `include` agar kode header & navigasi bawah tidak duplikat).*

---

## 3. Rincian Isi & Spesifikasi Tiap Halaman

---

### 1. [AuthLogin.html](file:///d:/Belajar/Appscript/tryone/views/AuthLogin.html) (`views/auth/login.php`)
- **Tujuan**: Pintu masuk aplikasi untuk memilih apakah pengguna ingin masuk sebagai **Admin** atau sebagai salah satu **Akun Kendaraan**.
- **Variabel Data**: `kendaraanList` (array seluruh kendaraan), `flash`.
- **Isi Halaman**:
  - Card tengah layar (`min-vh-100`) dengan judul **Pilih Akun Masuk**.
  - Alert flash message (jika ada pesan error/sukses).
  - Form `POST ?page=login&action=authenticate`:
    - Dropdown `<select name="akun">`:
      - `<optgroup label="Administrator">`: opsi `value="admin"`.
      - `<optgroup label="Akun Kendaraan">`: looping `kendaraanList` menampilkan `Nama_Kendaraan (Nomor_Polisi)`.
    - Tombol submit **Masuk** (dilengkapi animasi spinner saat disubmit).

---

### 2. [DaftarKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/DaftarKendaraan.html) (`views/kendaraan/index.php`)
- **Tujuan**: Menampilkan seluruh akun kendaraan yang terdaftar di sistem pada panel Admin.
- **Variabel Data**: `kendaraanList`, `flash`.
- **Isi Halaman**:
  - Header & Bottom Nav **Admin** (menu: *Daftar Kendaraan* aktif, *Master Komponen*, *Keluar*).
  - Header konten dengan tombol **+ Tambah Kendaraan** (`?page=kendaraan&action=create`).
  - Grid kartu kendaraan (`col-12 col-md-6 col-lg-4`):
    - Menampilkan `Nama_Kendaraan`, `Jenis_Kendaraan`, badge `Nomor_Polisi`, `KM_Aktual` (diformat dengan `formatKM()`), dan `Tgl_Update_KM`.

---

### 3. [BuatKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/BuatKendaraan.html) (`views/kendaraan/create.php`)
- **Tujuan**: Form bagi Admin untuk mendaftarkan akun kendaraan baru.
- **Isi Halaman**:
  - Form `POST ?page=kendaraan&action=store`:
    1. Input teks `Nama_Kendaraan` (wajib).
    2. Dropdown `Jenis_Kendaraan` (wajib) dengan **5 pilihan**:
       - `Motor Matic`
       - `Motor Bebek`
       - `Motor Sport`
       - `Mobil Matic`
       - `Mobil Manual`
    3. Input teks `Nomor_Polisi` (wajib).
    4. Input angka `KM_Aktual` (`min="0"`, default `0`).
    5. Tombol **Batal** & **Simpan Kendaraan**.

---

### 4. [DaftarKomponen.html](file:///d:/Belajar/Appscript/tryone/views/DaftarKomponen.html) (`views/komponen/index.php`)
- **Tujuan**: Menampilkan katalog seluruh Master Komponen pada panel Admin.
- **Variabel Data**: `komponenList`, `flash`.
- **Isi Halaman**:
  - Header & tombol **+ Tambah Komponen** (`?page=komponen&action=create`).
  - Grid kartu komponen (`col-12 col-md-6 col-lg-4`):
    - Badge `Kode_Komponen` (`bg-secondary`) dan badge **`Kategori`** (`bg-dark`, contoh: `Motor Matic`, `Mobil Manual`).
    - Judul `Nama_Komponen` dan tombol **Edit** (`?page=komponen&action=edit&id=...`).
    - Teks `Keterangan` (jika ada).
    - Footer kartu menampilkan **Interval**: hasil dari `formatIntervalKomponen(item)` (misal: `Cek: 2.000 KM | Ganti: 6.000 KM`).

---

### 5. [BuatKomponen.html](file:///d:/Belajar/Appscript/tryone/views/BuatKomponen.html) (`views/komponen/create.php`)
- **Tujuan**: Form bagi Admin untuk membuat item Master Komponen baru.
- **Isi Halaman**:
  - Form `POST ?page=komponen&action=store`:
    1. Input teks `Kode_Komponen` (wajib, contoh: `KMP-001`).
    2. Input teks `Nama_Komponen` (wajib, contoh: `Oli Mesin`).
    3. Dropdown **`Kategori`** (wajib) dengan 5 pilihan jenis kendaraan (`Motor Matic`, `Motor Bebek`, `Motor Sport`, `Mobil Matic`, `Mobil Manual`).
    4. Input angka **`Interval_Cek_KM`** (`min="1"`, opsional — keterangan: *"Warning Kuning saat tercapai"*).
    5. Input angka **`Interval_Ganti_KM`** (`min="1"`, opsional — keterangan: *"Warning Merah saat tercapai. Minimal isi salah satu (Cek atau Ganti)"*).
    6. Textarea `Keterangan` (opsional).
    7. Tombol **Batal** & **Simpan Komponen**.

---

### 6. [UbahKomponen.html](file:///d:/Belajar/Appscript/tryone/views/UbahKomponen.html) (`views/komponen/edit.php`)
- **Tujuan**: Form bagi Admin untuk mengubah data Master Komponen yang sudah ada.
- **Variabel Data**: `komponen`, `flash`.
- **Isi Halaman**:
  - Form `POST ?page=komponen&action=update` dengan `<input type="hidden" name="ID_Komponen">`.
  - Kolom input sama persis dengan `BuatKomponen.html` yang sudah terisi nilai lama (`Kode_Komponen`, `Nama_Komponen`, dropdown `Kategori` terpilih otomatis, `Interval_Cek_KM`, `Interval_Ganti_KM`, dan `Keterangan`).

---

### 7. [Dashboard.html](file:///d:/Belajar/Appscript/tryone/views/Dashboard.html) (`views/dashboard/index.php`)
- **Tujuan**: Halaman beranda utama Akun Kendaraan untuk mengecek odometer baru dan melihat komponen apa saja yang harus segera diganti (Merah) atau dicek (Kuning).
- **Variabel Data**: `kendaraan`, `komponenEvaluasi` (sudah diurutkan berdasarkan prioritas warning), `flash`.
- **Isi Halaman**:
  - **Kolom Kiri / Atas (`col-12 col-lg-4`)**:
    1. **Kartu Profil Kendaraan**: Menampilkan `Nomor_Polisi`, `Nama_Kendaraan`, `Jenis_Kendaraan`, tombol **Ubah Data** (`?page=kendaraan&action=edit`), kotak biru `KM_Aktual` terakhir, dan `Tgl_Update_KM`.
    2. **Kartu Form Cek KM**: Form `POST ?page=dashboard&action=cek_km` berisi input angka `km_odometer` (`min="<?= kendaraan.KM_Aktual ?>"`), dan tombol **Update & Cek KM**.
  - **Kolom Kanan / Bawah (`col-12 col-lg-8`)**:
    - Header **Status Komponen** beserta tombol hijau **+ Catat Servis** (`?page=service&action=create`).
    - **Daftar Kartu Evaluasi Komponen** (tampil berurutan dari **Warning Merah** $\rightarrow$ **Warning Kuning** $\rightarrow$ **Aman** $\rightarrow$ **Belum Ada Riwayat**):
      - **Pewarnaan Kartu & Badge**:
        - Status `Harus Diganti`: kartu `bg-danger bg-opacity-10`, badge `bg-danger`.
        - Status `Perlu Dicek`: kartu `bg-warning bg-opacity-10`, badge `bg-warning text-dark`.
        - Status `Aman`: kartu putih normal, badge `bg-success`.
        - Status `Belum Ada Riwayat`: kartu putih normal, badge `bg-secondary`.
      - **Informasi Riwayat**: Menampilkan patokan interval (`formatIntervalKomponen`), `Cek Terakhir: ... KM` dan/atau `Ganti Terakhir: ... KM`.
      - **Informasi Target & Sisa KM** (di bagian bawah kartu):
        - Jika komponen memiliki `Target_KM_Cek`, tampilkan baris `Target Cek: ... KM` dan `Sisa: ... KM`.
        - Jika komponen memiliki `Target_KM_Ganti`, tampilkan baris `Target Ganti: ... KM` dan `Sisa: ... KM`.

---

### 8. [UbahKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/UbahKendaraan.html) (`views/kendaraan/edit.php`)
- **Tujuan**: Form bagi pemilik akun kendaraan untuk memperbarui profil kendaraannya sendiri.
- **Variabel Data**: `kendaraan`, `flash`.
- **Isi Halaman**:
  - Form `POST ?page=kendaraan&action=update` berisi input `Nama_Kendaraan`, dropdown `Jenis_Kendaraan` (5 pilihan kategori), `Nomor_Polisi`, dan `KM_Aktual`.

---

### 9. [DaftarKomponenKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/DaftarKomponenKendaraan.html) (`views/kendaraan_komponen/index.php`)
- **Tujuan**: Menampilkan daftar seluruh komponen yang dipantau pada kendaraan aktif beserta statistik jumlah riwayat servisnya.
- **Variabel Data**: `komponenKendaraanList`, `flash`.
- **Isi Halaman**:
  - Header & tombol **+ Komponen Baru** (`?page=kendaraan_komponen&action=create`).
  - Grid kartu komponen kendaraan (`col-12 col-md-6 col-lg-4`):
    - Badge `Kode_Komponen`.
    - **Badge Statistik Riwayat**:
      - Jika `Total_Riwayat > 0`: menampilkan **`X Riwayat (Yx Ganti, Zx Cek)`** (contoh: `3 Riwayat (2x Ganti, 1x Cek)`).
      - Jika `Total_Riwayat === 0`: menampilkan **`0 Riwayat`**.
    - Nama komponen, teks `Interval: ...`, dan tombol **Hapus** (`POST ?page=kendaraan_komponen&action=delete`).
    - Informasi `Cek Terakhir` (KM & Tanggal) serta `Ganti Terakhir` (KM & Tanggal).
    - Tombol **Lihat Riwayat & Interval KM** (`?page=kendaraan_komponen&action=show&id=...`).

---

### 10. [BuatKomponenKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/BuatKomponenKendaraan.html) (`views/kendaraan_komponen/create.php`)
- **Tujuan**: Memilih komponen dari Master Komponen untuk mulai dipantau pada kendaraan aktif.
- **Variabel Data**: `masterKomponenList` (hanya berisi komponen yang `Kategori`-nya sesuai `Jenis_Kendaraan` aktif dan belum terdaftar di kendaraan ini).
- **Isi Halaman**:
  - Form `POST ?page=kendaraan_komponen&action=store` dengan dropdown `<select name="ID_Komponen">` dan tombol **Tambahkan ke Kendaraan**.

---

### 11. [DetailKomponenKendaraan.html](file:///d:/Belajar/Appscript/tryone/views/DetailKomponenKendaraan.html) (`views/kendaraan_komponen/show.php`)
- **Tujuan**: Menampilkan informasi detail suatu komponen pada kendaraan aktif, rata-rata interval/selisih KM pemakaian aktual, serta tabel lengkap riwayat kapan saja komponen tersebut diganti atau dicek.
- **Variabel Data**: `komponenKendaraan`, `komponen`, `riwayatList`, `rataRataSelisihKm`, `flash`.
- **Isi Halaman**:
  1. **Header Halaman**: Badge `Kode_Komponen`, badge `Kategori`, judul `Riwayat Komponen: {Nama_Komponen}`, dan tombol **Kembali**.
  2. **Kartu Ringkasan Komponen (Di Atas Tabel)**:
     - **Keterangan Komponen**: Menampilkan isi `komponen.Keterangan` dari tabel Master Komponen.
     - **Interval Rekomendasi**: Menampilkan `formatIntervalKomponen(komponen)`.
     - **Rata-rata Interval / Selisih KM**: Menampilkan rata-rata selisih KM dari seluruh riwayat di tabel (`formatKM(rataRataSelisihKm)`), atau teks *"Minimal 2 riwayat servis"* jika belum cukup data.
  3. **Tabel Riwayat Penggantian & Pengecekan (`table-responsive`)**:
     - Kolom tabel:
       1. `No`
       2. `Tanggal Servis`
       3. `Tindakan` (Badge biru `Ganti` atau kuning `Cek`)
       4. `KM Saat Servis`
       5. `Interval / Selisih KM` (menampilkan `+X KM` beserta keterangan acuan `dari Ganti di ... KM` atau `dari Cek/Ganti di ... KM`, atau badge `Ganti Pertama` / `Cek Pertama`)
       6. `Lokasi Bengkel`
       7. `Biaya` (`Subtotal_Harga`)
       8. `Keterangan` (catatan item servis)
       9. `Servis` (Tombol pintas `#ID_Service` menuju `?page=service&action=show&id=...`)

---

### 12. [DaftarService.html](file:///d:/Belajar/Appscript/tryone/views/DaftarService.html) (`views/service/index.php`)
- **Tujuan**: Menampilkan daftar seluruh riwayat kunjungan servis kendaraan aktif beserta penanda urutan servis dari pertama hingga terakhir.
- **Variabel Data**: `serviceList` (sudah dilengkapi `Urutan_Service` dan `Total_Service_Kendaraan`), `flash`.
- **Isi Halaman**:
  - Header & tombol **+ Catat Servis** (`?page=service&action=create`).
  - Grid kartu servis (`col-12 col-md-6`):
    - **Badge Urutan Kronologis**:
      - Badge **`Servis ke-{Urutan_Service}`** (`bg-dark`).
      - Badge tambahan **`Servis Terakhir`** (`bg-success`) untuk servis urutan paling akhir, atau **`Servis Pertama`** (`bg-secondary`) untuk servis urutan ke-1.
    - `Tanggal_Service`, `Lokasi_Bengkel`, `KM_Service`, badge `Total_Biaya`, dan `Catatan_Service`.
    - Baris tombol aksi di bagian bawah kartu:
      - Tombol **Lihat Detail & Komponen** (`?page=service&action=show&id=...`).
      - Tombol **Nota** (`target="_blank"` membuka foto nota jika kolom `Nota` terisi).
      - Tombol **Ubah** (`?page=service&action=edit&id=...`).

---

### 13. [BuatService.html](file:///d:/Belajar/Appscript/tryone/views/BuatService.html) (`views/service/create.php`)
- **Tujuan**: Mencatat transaksi header servis baru (termasuk servis mundur/sebelum odometer saat ini) dan mengunggah foto nota servis.
- **Variabel Data**: `kendaraan`, `todayDate`, `flash`.
- **Isi Halaman**:
  - Form `POST ?page=service&action=store`:
    1. Input `Tanggal_Service` (`type="date"`, default hari ini).
    2. Input `KM_Service` (`type="number"`, **`min="0"`**, default `kendaraan.KM_Aktual`, dengan penjelasan bahwa KM Aktual kendaraan hanya akan diperbarui jika KM servis lebih besar).
    3. Input `Lokasi_Bengkel` (wajib).
    4. Input `Biaya_Jasa` (`min="0"`, default `0`).
    5. Textarea `Catatan_Service` (opsional).
    6. Input file **Foto Nota Servis** (`<input type="file" accept="image/*">`):
       - Pada versi Apps Script: otomatis dikompresi via `<canvas>` (maks `1280px` JPEG) dan dikirim sebagai Base64 (`Nota_Base64`, `Nota_Mime`) untuk diunggah ke Google Drive.
       - Pada versi PHP Native: dikirim via `enctype="multipart/form-data"` (`name="file_nota"`) untuk disimpan ke folder `uploads/nota/` (atau Google Drive API).

---

### 14. [UbahService.html](file:///d:/Belajar/Appscript/tryone/views/UbahService.html) (`views/service/edit.php`)
- **Tujuan**: Mengubah data header transaksi servis yang sudah pernah dibuat atau mengganti/menambahkan foto nota servis.
- **Variabel Data**: `service`, `kendaraan`, `flash`.
- **Isi Halaman**:
  - Form `POST ?page=service&action=update` dengan `<input type="hidden" name="ID_Service">`.
  - Seluruh kolom terisi nilai `service` saat ini (`Tanggal_Service`, `KM_Service` dengan `min="0"`, `Lokasi_Bengkel`, `Biaya_Jasa`, `Catatan_Service`).
  - Jika `service.Nota` sudah ada, menampilkan tombol **Lihat Foto Nota Saat Ini** di atas input file upload foto nota baru.

---

### 15. [DetailService.html](file:///d:/Belajar/Appscript/tryone/views/DetailService.html) (`views/service/show.php`)
- **Tujuan**: Menampilkan rincian informasi & biaya pada 1 transaksi servis, sekaligus menyediakan form untuk mencatat komponen apa saja yang **Diganti (`Ganti`)** atau **Diperiksa (`Cek`)** pada servis tersebut.
- **Variabel Data**: `service`, `detailKomponenList`, `masterKomponenList` (sudah tersaring sesuai `Jenis_Kendaraan` aktif), `flash`.
- **Isi Halaman**:
  - **Kolom Kiri / Atas (`col-12 col-lg-4`)**:
    1. **Kartu Informasi & Biaya Servis**:
       - Tombol **Ubah** di sudut kanan header kartu (`?page=service&action=edit&id=...`).
       - Menampilkan `Tanggal_Service`, `Lokasi_Bengkel`, `KM_Service`, `Catatan_Service`, tombol **Lihat Foto Nota** (jika `service.Nota` ada), `Biaya_Jasa`, `Total_Biaya_Komponen`, dan `Total_Biaya`.
    2. **Kartu Form Tambah Komponen Servis (Cek / Ganti)**:
       - Form `POST ?page=service_komponen&action=store` (`ID_Service`):
         - Dropdown `ID_Komponen` (hanya menampilkan komponen yang sesuai dengan `Jenis_Kendaraan` kendaraan aktif).
         - Dropdown **`Tindakan`**:
           - `Ganti` (*Ganti - Reset KM Ganti & KM Cek*)
           - `Cek` (*Cek - Reset KM Cek Saja*)
         - Input `Qty` (default `1`) dan `Harga_Satuan` (default `0`).
         - Input `Keterangan` (opsional) dan tombol **+ Simpan Tindakan Komponen**.
  - **Kolom Kanan / Bawah (`col-12 col-lg-8`)**:
    - **Daftar Komponen Servis (Cek / Ganti)**:
      - Menampilkan kartu untuk setiap item di `detailKomponenList` dengan badge `Kode_Komponen`, badge kategori tindakan (`Ganti` biru / `Cek` kuning), `Nama_Komponen`, `Qty x Harga_Satuan`, `Keterangan`, `Subtotal_Harga`, serta tombol **Hapus**.
