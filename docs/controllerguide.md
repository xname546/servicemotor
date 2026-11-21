# Controller Guide: Arsitektur Controller & Spesifikasi Fungsi

Dokumen ini menjelaskan pemetaan modul dan fungsi dari [Kode.gs](file:///d:/Belajar/Appscript/tryone/controllers/Kode.gs) serta [Routing.gs](file:///d:/Belajar/Appscript/tryone/routes/Routing.gs) untuk diterapkan kembali ke dalam struktur file Controller versi **PHP Native** (`controllers/*.php`, `database/connection.php`, dan `routes/web.php`).

---

## 1. Pemetaan File Backend (Apps Script $\leftrightarrow$ PHP Native)

| Modul di Apps Script | File di Versi PHP Native | Tanggung Jawab Utama |
|---|---|---|
| `Database.gs` + Helper global di `Kode.gs` & `Routing.gs` | `database/connection.php` | Koneksi database (`getDB`), session, flash message (`setFlash`, `getFlash`), format angka/label (`formatRupiah`, `formatKM`, `formatIntervalKomponen`), & helper upload foto nota (`uploadNota`). |
| `Routing.gs` (`handleRequest`) | `routes/web.php` & `index.php` | Menerima request `$_GET['page']` dan `$_GET['action']`, lalu memanggil method Controller yang sesuai. |
| `AuthController` (`Kode.gs`) | `controllers/AuthController.php` | Halaman pilih akun masuk (Admin / Kendaraan), proses autentikasi session, dan logout. |
| `DashboardController` (`Kode.gs`) | `controllers/DashboardController.php` | Beranda akun kendaraan, evaluasi status warna komponen (Merah/Kuning/Hijau/Abu-abu), pengurutan prioritas warning, dan update Cek KM odometer baru. |
| `KendaraanController` (`Kode.gs`) | `controllers/KendaraanController.php` | **Admin**: Daftar & tambah akun kendaraan baru.<br>**Kendaraan**: Form & simpan perubahan profil kendaraan aktif. |
| `KomponenController` (`Kode.gs`) | `controllers/KomponenController.php` | **Admin**: Daftar, tambah, dan edit master komponen (termasuk `Kategori`, `Interval_Cek_KM`, dan `Interval_Ganti_KM`). |
| `KendaraanKomponenController` (`Kode.gs`) | `controllers/KendaraanKomponenController.php` | **Kendaraan**: Daftar komponen yang dipantau beserta statistik jumlah riwayat (`Total_Riwayat`, `Jumlah_Ganti`, `Jumlah_Cek`), tambah komponen sesuai kategori kendaraan, detail tabel riwayat & rata-rata selisih KM (`show`), serta hapus komponen kendaraan. |
| `ServiceController` (`Kode.gs`) | `controllers/ServiceController.php` & `controllers/ServiceKomponenController.php` | **Kendaraan**: Daftar servis beserta nomor urut kronologis (`Servis ke-1`..`ke-N`), tambah servis baru + upload foto nota, edit header servis (`edit` & `update`), detail servis (`show`), tambah tindakan komponen servis (`Cek`/`Ganti`), hapus tindakan komponen servis, serta sinkronisasi otomatis ke `Kendaraan_Komponen`. |

---

## 2. Helper Global pada `database/connection.php`

Selain koneksi PDO `getDB()`, `setFlash($type, $message)`, `getFlash()`, `formatRupiah($angka)`, dan `formatKM($angka)`, tambahkan **2 fungsi helper baru** berikut di `database/connection.php`:

### A. `formatIntervalKomponen(array $komp): string`
- **Tujuan**: Merender teks ringkas interval komponen baik yang hanya punya Interval Cek, hanya punya Interval Ganti, maupun keduanya sekaligus.
- **Logika**:
  - Ambil `$cek = (int)($komp['Interval_Cek_KM'] ?? 0);`
  - Ambil `$ganti = (int)($komp['Interval_Ganti_KM'] ?? 0);`
  - Jika `$cek > 0`, tambahkan `'Cek: ' . formatKM($cek)` ke array `$parts`.
  - Jika `$ganti > 0`, tambahkan `'Ganti: ' . formatKM($ganti)` ke array `$parts`.
  - Gabungkan dengan `' | '` (atau kembalikan `'-'` jika kosong).

### B. `uploadNotaFile(int $idKendaraan, string $tanggal): ?string`
- **Tujuan**: Menyimpan file foto nota yang diunggah dari `<input type="file" name="file_nota">` pada form tambah/edit servis.
- **Logika di PHP**:
  - Periksa apakah `$_FILES['file_nota']['error'] === UPLOAD_ERR_OK`.
  - Validasi ekstensi/MIME gambar (`jpg`, `jpeg`, `png`, `webp`).
  - Buat folder `uploads/nota/` jika belum ada.
  - Simpan dengan nama unik `Nota_Kendaraan{$idKendaraan}_{$tanggal}_" . time() . ".{$ext}`.
  - Kembalikan path relatif `'uploads/nota/' . $fileName` untuk disimpan ke kolom `Nota` di tabel `Service` (atau `null` jika tidak ada file yang diunggah).

---

## 3. Rincian Setiap File Controller & Fungsi di Dalamnya

---

### File 1: `controllers/AuthController.php`

Mengelola pemilihan akun tanpa password (dropdown Admin atau Akun Kendaraan).

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `login(): void` | `GET ?page=login` | Mengambil seluruh daftar kendaraan dari tabel `Kendaraan` (`ORDER BY Nama_Kendaraan ASC`), lalu menampilkan view `views/auth/login.php`. |
| `authenticate(): void` | `POST ?page=login&action=authenticate` | 1. Membaca input `akun`. Jika kosong, beri flash `danger` dan kembali ke `login`.<br>2. Jika `akun === 'admin'`, set `$_SESSION['role'] = 'admin'`, hapus session kendaraan, lalu arahkan ke `?page=kendaraan`.<br>3. Jika `akun` berupa ID kendaraan, ambil baris dari tabel `Kendaraan`. Simpan `role = 'kendaraan'`, `kendaraan_id`, `kendaraan_nama`, dan `kendaraan_nopol` ke `$_SESSION`, lalu arahkan ke `?page=dashboard`. |
| `logout(): void` | `GET ?page=logout` | Menghapus seluruh data session (`session_unset()`, `session_destroy()`) dan mengarahkan kembali ke `?page=login`. |

---

### File 2: `controllers/DashboardController.php`

Mengelola halaman utama akun kendaraan, pengecekan odometer baru, evaluasi status komponen (Merah, Kuning, Hijau, Abu-abu), serta pengurutan kartu berdasarkan tingkat urgensi warning.

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `getActiveKendaraanId(): int` *(private)* | Internal | Memastikan `$_SESSION['role'] === 'kendaraan'` dan mengembalikan `(int)$_SESSION['kendaraan_id']`. Jika belum login sebagai kendaraan, redirect ke `?page=login`. |
| `index(): void` | `GET ?page=dashboard` | 1. Mengambil data profil kendaraan aktif dari tabel `Kendaraan`.<br>2. Mengambil daftar komponen kendaraan (`Kendaraan_Komponen JOIN Komponen`) beserta kolom `Interval_Cek_KM` dan `Interval_Ganti_KM`.<br>3. **Evaluasi Target & Status tiap komponen**:<br>   - `kmTerakhirGanti = KM_Terakhir_Ganti` (jika ada)<br>   - `kmTerakhirCek = KM_Terakhir_Cek ?? kmTerakhirGanti` (jika `KM_Terakhir_Cek` kosong tetapi sudah pernah ganti, gunakan `KM_Terakhir_Ganti` sebagai patokan awal cek).<br>   - Jika `Interval_Cek_KM > 0` dan `kmTerakhirCek !== null`:<br>     $\text{Target\_KM\_Cek} = \text{kmTerakhirCek} + \text{Interval\_Cek\_KM}$<br>     $\text{Sisa\_KM\_Cek} = \text{Target\_KM\_Cek} - \text{KM\_Aktual}$<br>   - Jika `Interval_Ganti_KM > 0` dan `kmTerakhirGanti !== null`:<br>     $\text{Target\_KM\_Ganti} = \text{kmTerakhirGanti} + \text{Interval\_Ganti\_KM}$<br>     $\text{Sisa\_KM} = \text{Target\_KM\_Ganti} - \text{KM\_Aktual}$<br>   - **Penentuan Status**:<br>     - Jika `Target_KM_Cek === null` dan `Target_KM_Ganti === null` $\rightarrow$ **`Belum Ada Riwayat`** (Abu-abu).<br>     - Jika `Sisa_KM !== null` dan `Sisa_KM <= 0` $\rightarrow$ **`Harus Diganti`** (Warning Merah — prioritas tertinggi).<br>     - Jika `Sisa_KM_Cek !== null` dan `Sisa_KM_Cek <= 0` $\rightarrow$ **`Perlu Dicek`** (Warning Kuning).<br>     - Selain itu $\rightarrow$ **`Aman`** (Hijau).<br>4. **Pengurutan Prioritas (`usort`)**:<br>   Urutkan array `$komponenEvaluasi` berdasarkan prioritas status: `Harus Diganti (1)` $\rightarrow$ `Perlu Dicek (2)` $\rightarrow$ `Aman (3)` $\rightarrow$ `Belum Ada Riwayat (4)`, lalu berdasarkan `Kode_Komponen ASC`.<br>5. Render view `views/dashboard/index.php`. |
| `cekKm(): void` | `POST ?page=dashboard&action=cek_km` | 1. Mengambil `KM_Aktual` kendaraan aktif dan membandingkannya dengan input `km_odometer`.<br>2. Validasi: `km_odometer` tidak boleh lebih kecil dari `KM_Aktual` saat ini.<br>3. Update `KM_Aktual = km_odometer` dan `Tgl_Update_KM = CURDATE()` pada tabel `Kendaraan`.<br>4. Beri flash `success` dan redirect ke `?page=dashboard`. |

---

### File 3: `controllers/KendaraanController.php`

Digunakan oleh **Admin** (untuk melihat daftar kendaraan & menambah kendaraan baru) dan oleh **Akun Kendaraan** (untuk mengubah data profil kendaraannya sendiri).

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `ensureAdmin(): void` *(private)* | Internal | Memastikan session aktif adalah `admin`. |
| `getActiveKendaraanId(): int` *(private)* | Internal | Mengambil ID kendaraan aktif dari session. |
| `index(): void` | `GET ?page=kendaraan` | **(Admin)** Mengambil seluruh baris `Kendaraan` (`ORDER BY ID_Kendaraan DESC`) dan menampilkan `views/kendaraan/index.php`. |
| `create(): void` | `GET ?page=kendaraan&action=create` | **(Admin)** Menampilkan form tambah kendaraan `views/kendaraan/create.php` dengan 5 pilihan `Jenis_Kendaraan` (`Motor Matic`, `Motor Bebek`, `Motor Sport`, `Mobil Matic`, `Mobil Manual`). |
| `store(): void` | `POST ?page=kendaraan&action=store` | **(Admin)** Memvalidasi input `Nama_Kendaraan`, `Jenis_Kendaraan`, `Nomor_Polisi` (wajib unik & di-uppercase), dan `KM_Aktual`. Menyimpan ke tabel `Kendaraan` dengan `Tgl_Update_KM = CURDATE()`. |
| `edit(): void` | `GET ?page=kendaraan&action=edit` | **(Kendaraan)** Mengambil data kendaraan yang sedang login dan menampilkan form `views/kendaraan/edit.php`. |
| `update(): void` | `POST ?page=kendaraan&action=update` | **(Kendaraan)** Memvalidasi perubahan `Nama_Kendaraan`, `Jenis_Kendaraan`, `Nomor_Polisi` (unik selain ID dirinya sendiri), serta `KM_Aktual`. Mengupdate tabel `Kendaraan`, memperbarui `$_SESSION['kendaraan_nama']` & `$_SESSION['kendaraan_nopol']`, lalu mengarahkan ke `?page=dashboard`. |

---

### File 4: `controllers/KomponenController.php`

Digunakan oleh **Admin** untuk mengelola katalog Master Komponen beserta kategori kendaraan dan kedua jenis interval KM.

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `ensureAdmin(): void` *(private)* | Internal | Memastikan session aktif adalah `admin`. |
| `index(): void` | `GET ?page=komponen` | Mengambil seluruh master komponen (`ORDER BY Kode_Komponen ASC`) dan menampilkan `views/komponen/index.php`. |
| `create(): void` | `GET ?page=komponen&action=create` | Menampilkan form tambah master komponen `views/komponen/create.php`. |
| `store(): void` | `POST ?page=komponen&action=store` | 1. Membaca `Kode_Komponen` (uppercase), `Nama_Komponen`, `Kategori`, `Interval_Cek_KM` (nullable), `Interval_Ganti_KM` (nullable), dan `Keterangan`.<br>2. **Validasi**:<br>   - `Kode_Komponen`, `Nama_Komponen`, dan `Kategori` wajib diisi.<br>   - Minimal salah satu dari `Interval_Cek_KM` atau `Interval_Ganti_KM` harus `> 0`.<br>   - Jika keduanya diisi (`> 0`), pastikan `Interval_Cek_KM < Interval_Ganti_KM`.<br>3. Simpan ke tabel `Komponen` (nilai interval `<= 0` disimpan sebagai `NULL`). |
| `edit(): void` | `GET ?page=komponen&action=edit&id={id}` | Mengambil data 1 komponen berdasarkan `ID_Komponen` dan menampilkan form `views/komponen/edit.php`. |
| `update(): void` | `POST ?page=komponen&action=update` | Memvalidasi input dengan aturan yang sama seperti `store()`, memastikan `Kode_Komponen` tidak bentrok dengan komponen lain, lalu mengupdate baris di tabel `Komponen`. |

---

### File 5: `controllers/KendaraanKomponenController.php`

Digunakan oleh **Akun Kendaraan** untuk melihat daftar komponen yang dipantau beserta jumlah riwayat servisnya, menambahkan komponen baru sesuai kategori kendaraan, dan melihat halaman detail tabel riwayat & rata-rata selisih KM komponen.

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `getActiveKendaraanId(): int` *(private)* | Internal | Mengambil ID kendaraan aktif dari session. |
| `index(): void` | `GET ?page=kendaraan_komponen` | 1. Mengambil daftar `Kendaraan_Komponen` milik kendaraan aktif (`JOIN Komponen`).<br>2. Menghitung statistik riwayat servis dari `Service_Komponen JOIN Service` untuk masing-masing komponen pada kendaraan tersebut:<br>   - `Total_Riwayat`: total berapa kali komponen tercatat di servis.<br>   - `Jumlah_Ganti`: berapa kali dengan `Tindakan = 'Ganti'`.<br>   - `Jumlah_Cek`: berapa kali dengan `Tindakan = 'Cek'`.<br>3. Menampilkan `views/kendaraan_komponen/index.php` (`ORDER BY Kode_Komponen ASC`). |
| `create(): void` | `GET ?page=kendaraan_komponen&action=create` | 1. Mengambil `Jenis_Kendaraan` dari kendaraan aktif.<br>2. Mengambil daftar `Komponen` yang **`Kategori = Jenis_Kendaraan`** dan **belum terdaftar** di `Kendaraan_Komponen` milik kendaraan ini.<br>3. Menampilkan `views/kendaraan_komponen/create.php`. |
| `store(): void` | `POST ?page=kendaraan_komponen&action=store` | Menyimpan `ID_Kendaraan` dan `ID_Komponen` ke tabel `Kendaraan_Komponen` dengan `KM_Terakhir_Cek = NULL`, `Tanggal_Terakhir_Cek = NULL`, `KM_Terakhir_Ganti = NULL`, `Tanggal_Terakhir_Ganti = NULL`. |
| `show(): void` | `GET ?page=kendaraan_komponen&action=show&id={id_kk}` | 1. Memvalidasi baris `Kendaraan_Komponen` berdasarkan `id` dan `ID_Kendaraan` aktif, serta mengambil detail master `Komponen` (`Kode_Komponen`, `Nama_Komponen`, `Kategori`, `Interval_Cek_KM`, `Interval_Ganti_KM`, `Keterangan`).<br>2. Mengambil seluruh riwayat tindakan komponen ini dari `Service_Komponen JOIN Service` milik kendaraan aktif, diurutkan secara kronologis dari terlama ke terbaru (`KM_Service ASC, Tanggal_Service ASC, ID_Service ASC`).<br>3. **Menghitung `Selisih_KM` (Interval pemakaian aktual) pada setiap baris**:<br>   - Siapkan variabel `$prevGantiKm = null` dan `$prevAnyKm = null`.<br>   - Jika baris bertipe **`Ganti`**:<br>     - Jika `$prevGantiKm !== null`, maka `Selisih_KM = KM_Service - $prevGantiKm` dan `Acuan_Label = 'dari Ganti di ' . formatKM($prevGantiKm)`.<br>     - Jika `$prevGantiKm === null`, maka `Selisih_KM = null` dan `Acuan_Label = 'Ganti Pertama'`.<br>     - Update `$prevGantiKm = KM_Service` dan `$prevAnyKm = KM_Service`.<br>   - Jika baris bertipe **`Cek`**:<br>     - Jika `$prevAnyKm !== null`, maka `Selisih_KM = KM_Service - $prevAnyKm` dan `Acuan_Label = 'dari Cek/Ganti di ' . formatKM($prevAnyKm)`.<br>     - Jika `$prevAnyKm === null`, maka `Selisih_KM = null` dan `Acuan_Label = 'Cek Pertama'`.<br>     - Update `$prevAnyKm = KM_Service`.<br>4. Balik urutan array (`array_reverse`) agar servis terbaru berada di baris paling atas tabel.<br>5. **Menghitung `rataRataSelisihKm`**:<br>   Hitung rata-rata (`round(sum / count)`) dari seluruh baris yang memiliki `Selisih_KM !== null` (atau `null` jika belum ada baris yang memiliki selisih).<br>6. Render view `views/kendaraan_komponen/show.php`. |
| `delete(): void` | `POST ?page=kendaraan_komponen&action=delete` | Menghapus komponen dari daftar pantau `Kendaraan_Komponen` milik kendaraan aktif. |

---

### File 6: `controllers/ServiceController.php` & `controllers/ServiceKomponenController.php`

*(Catatan: Pada versi Apps Script keduanya digabung di dalam objek `ServiceController` pada `Kode.gs`. Pada versi PHP dapat digabung di `ServiceController.php` atau dipisah antara `ServiceController.php` dan `ServiceKomponenController.php`).*

| Nama Function / Method | HTTP & Route | Tujuan & Alur Logika |
|---|---|---|
| `index(): void` | `GET ?page=service` | 1. Mengambil seluruh transaksi `Service` milik kendaraan aktif, diurutkan secara kronologis dari yang paling awal ke paling baru (`Tanggal_Service ASC, KM_Service ASC, ID_Service ASC`).<br>2. Memberikan nomor **`Urutan_Service = 1..N`** dan `Total_Service_Kendaraan = N` pada tiap baris.<br>3. Membalik urutan array (`array_reverse`) agar servis paling akhir tampil paling atas.<br>4. Menampilkan `views/service/index.php`. |
| `create(): void` | `GET ?page=service&action=create` | Mengambil data kendaraan aktif dan menampilkan form `views/service/create.php`. |
| `store(): void` | `POST ?page=service&action=store` | 1. Membaca `Tanggal_Service`, `KM_Service` (`>= 0`, **boleh lebih kecil dari `KM_Aktual`** untuk input servis lama/mundur), `Lokasi_Bengkel`, `Biaya_Jasa`, `Catatan_Service`, dan memproses upload foto nota (`Nota`).<br>2. Menyimpan baris baru ke tabel `Service` (`Total_Biaya_Komponen = 0`, `Total_Biaya = Biaya_Jasa`).<br>3. **Aturan Update Odometer Kendaraan**:<br>   Hanya mengupdate `KM_Aktual = KM_Service` dan `Tgl_Update_KM = Tanggal_Service` pada tabel `Kendaraan` **jika `KM_Service > KM_Aktual` kendaraan saat ini**.<br>4. Redirect ke halaman detail servis `?page=service&action=show&id={id_service}`. |
| `edit(): void` | `GET ?page=service&action=edit&id={id}` | Mengambil data `Service` berdasarkan `id` (milik kendaraan aktif) beserta profil `Kendaraan`, lalu menampilkan form `views/service/edit.php`. |
| `update(): void` | `POST ?page=service&action=update` | 1. Memvalidasi dan mengupdate `Tanggal_Service`, `Lokasi_Bengkel`, `KM_Service`, `Biaya_Jasa`, `Catatan_Service`, serta `Nota` (jika pengguna mengunggah foto nota baru; jika tidak, pertahankan foto nota lama).<br>2. Memanggil `recalculateServiceTotal()` untuk menghitung ulang `Total_Biaya`.<br>3. Jika `KM_Service > KM_Aktual` kendaraan, perbarui `KM_Aktual` dan `Tgl_Update_KM` pada `Kendaraan`.<br>4. Untuk setiap komponen yang tercatat di `Service_Komponen` pada servis ini, panggil `recalculateLastGantiKomponen()` agar `KM_Terakhir_Cek` & `KM_Terakhir_Ganti` di `Kendaraan_Komponen` ikut tersinkronisasi jika `KM_Service` atau `Tanggal_Service` berubah.<br>5. Redirect kembali ke `?page=service&action=show&id={id_service}`. |
| `show(): void` | `GET ?page=service&action=show&id={id}` | 1. Mengambil header transaksi `Service` milik kendaraan aktif.<br>2. Mengambil daftar `Service_Komponen JOIN Komponen` untuk `ID_Service` tersebut.<br>3. Mengambil daftar pilihan Master `Komponen` yang **`Kategori`-nya sama dengan `Jenis_Kendaraan` kendaraan aktif** (`ORDER BY Kode_Komponen ASC`) untuk dropdown tambah tindakan komponen.<br>4. Menampilkan `views/service/show.php`. |
| `storeKomponen(): void` | `POST ?page=service_komponen&action=store` | 1. Membaca `ID_Service`, `ID_Komponen`, `Tindakan` (`'Ganti'` atau `'Cek'`), `Qty`, `Harga_Satuan`, dan `Keterangan`.<br>2. Menghitung `Subtotal_Harga = Qty * Harga_Satuan` dan menyimpan ke tabel `Service_Komponen`.<br>3. Memanggil `recalculateServiceTotal($idService, $biayaJasa)`.<br>4. Memanggil `syncKendaraanKomponen($idKendaraan, $idKomponen, $tindakan, $kmService, $tanggalService)` untuk memastikan komponen terdaftar di `Kendaraan_Komponen` dan memperbarui `KM_Terakhir_Cek` / `KM_Terakhir_Ganti`. |
| `deleteKomponen(): void` | `POST ?page=service_komponen&action=delete` | 1. Menghapus baris `Service_Komponen` berdasarkan `ID_Service_Komponen`.<br>2. Memanggil `recalculateServiceTotal($idService, $biayaJasa)`.<br>3. Memanggil `recalculateLastGantiKomponen($idKendaraan, $idKomponen)` untuk menghitung ulang riwayat cek/ganti terakhir dari transaksi servis yang masih tersisa. |
| `recalculateServiceTotal()` *(private)* | Internal | Menghitung `SUM(Subtotal_Harga)` dari `Service_Komponen` pada `ID_Service` terkait, lalu mengupdate `Total_Biaya_Komponen` dan `Total_Biaya = Biaya_Jasa + Total_Biaya_Komponen` pada tabel `Service`. |
| `syncKendaraanKomponen()` & `recalculateLastGantiKomponen()` *(private)* | Internal | 1. Jika komponen belum ada di `Kendaraan_Komponen` untuk kendaraan tersebut, otomatis melakukan `INSERT`.<br>2. Mencari servis dengan `KM_Service` tertinggi di `Service_Komponen JOIN Service` milik kendaraan tersebut:<br>   - `KM_Terakhir_Cek` & `Tanggal_Terakhir_Cek`: diambil dari `KM_Service` tertinggi untuk semua tindakan (`'Cek'` maupun `'Ganti'`, karena penggantian komponen baru otomatis me-reset jadwal cek juga).<br>   - `KM_Terakhir_Ganti` & `Tanggal_Terakhir_Ganti`: diambil dari `KM_Service` tertinggi khusus tindakan **`'Ganti'`** saja. |

---

## 4. Tabel Routing (`routes/web.php` / `Routing.gs`)

| Parameter `page` | Parameter `action` | Method Controller yang Dipanggil |
|---|---|---|
| `login` | `index` *(default)* | `AuthController->login()` |
| `login` | `authenticate` | `AuthController->authenticate()` |
| `logout` | `index` *(default)* | `AuthController->logout()` |
| `dashboard` | `index` *(default)* | `DashboardController->index()` |
| `dashboard` | `cek_km` | `DashboardController->cekKm()` |
| `kendaraan` | `index` *(default)* | `KendaraanController->index()` |
| `kendaraan` | `create` | `KendaraanController->create()` |
| `kendaraan` | `store` | `KendaraanController->store()` |
| `kendaraan` | `edit` | `KendaraanController->edit()` |
| `kendaraan` | `update` | `KendaraanController->update()` |
| `komponen` | `index` *(default)* | `KomponenController->index()` |
| `komponen` | `create` | `KomponenController->create()` |
| `komponen` | `store` | `KomponenController->store()` |
| `komponen` | `edit` | `KomponenController->edit()` |
| `komponen` | `update` | `KomponenController->update()` |
| `kendaraan_komponen` | `index` *(default)* | `KendaraanKomponenController->index()` |
| `kendaraan_komponen` | `create` | `KendaraanKomponenController->create()` |
| `kendaraan_komponen` | `store` | `KendaraanKomponenController->store()` |
| `kendaraan_komponen` | `show` | `KendaraanKomponenController->show()` |
| `kendaraan_komponen` | `delete` | `KendaraanKomponenController->delete()` |
| `service` | `index` *(default)* | `ServiceController->index()` |
| `service` | `create` | `ServiceController->create()` |
| `service` | `store` | `ServiceController->store()` |
| `service` | `edit` | `ServiceController->edit()` |
| `service` | `update` | `ServiceController->update()` |
| `service` | `show` | `ServiceController->show()` |
| `service_komponen` | `store` | `ServiceController->storeKomponen()` / `ServiceKomponenController->store()` |
| `service_komponen` | `delete` | `ServiceController->deleteKomponen()` / `ServiceKomponenController->delete()` |
