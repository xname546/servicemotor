<?php include __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Pendaftaran Akun Kendaraan Baru</h2>
        <p class="text-muted small mb-0">Tambahkan profil kendaraan baru beserta kategori jenis kendaraannya</p>
    </div>
    <a href="index.php?page=kendaraan" class="btn btn-outline-secondary">&larr; Kembali ke Daftar Kendaraan</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Formulir Data Kendaraan</h5>
    </div>
    <div class="card-body p-4">
        <form action="index.php?page=kendaraan&amp;action=store" method="post">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label for="nama_kendaraan" class="form-label fw-semibold">Nama Kendaraan</label>
                    <input type="text" id="nama_kendaraan" name="Nama_Kendaraan" class="form-control" placeholder="Contoh: Honda Vario 150 / Toyota Avanza" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="jenis_kendaraan" class="form-label fw-semibold">Kategori Jenis Kendaraan</label>
                    <select id="jenis_kendaraan" name="Jenis_Kendaraan" class="form-select" required>
                        <option value="">-- Pilih Kategori Jenis Kendaraan --</option>
                        <option value="Motor Matic">Motor Matic</option>
                        <option value="Motor Bebek">Motor Bebek</option>
                        <option value="Motor Sport">Motor Sport</option>
                        <option value="Mobil Matic">Mobil Matic</option>
                        <option value="Mobil Manual">Mobil Manual</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="nomor_polisi" class="form-label fw-semibold">Nomor Polisi</label>
                    <input type="text" id="nomor_polisi" name="Nomor_Polisi" class="form-control" placeholder="Contoh: B 1234 XYZ" required>
                    <div class="form-text">Otomatis disimpan dalam huruf kapital dan wajib unik.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="km_aktual" class="form-label fw-semibold">KM Odometer Awal</label>
                    <div class="input-group">
                        <input type="number" id="km_aktual" name="KM_Aktual" class="form-control" min="0" value="0" required>
                        <span class="input-group-text">KM</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=kendaraan" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-dark fw-semibold px-4">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>
