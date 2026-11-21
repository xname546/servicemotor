<?php include __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Tambah Master Komponen Baru</h2>
        <p class="text-muted small mb-0">Tentukan kategori kendaraan serta patokan Interval Cek KM dan/atau Interval Ganti KM</p>
    </div>
    <a href="index.php?page=komponen" class="btn btn-outline-secondary">&larr; Kembali ke Master Komponen</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Formulir Master Komponen</h5>
    </div>
    <div class="card-body p-4">
        <form action="index.php?page=komponen&amp;action=store" method="post">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label for="kode_komponen" class="form-label fw-semibold">Kode Komponen</label>
                    <input type="text" id="kode_komponen" name="Kode_Komponen" class="form-control" placeholder="Contoh: MT-001 / KMP-001" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="nama_komponen" class="form-label fw-semibold">Nama Komponen</label>
                    <input type="text" id="nama_komponen" name="Nama_Komponen" class="form-control" placeholder="Contoh: Oli Mesin / V-Belt CVT" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="kategori" class="form-label fw-semibold">Kategori Kendaraan</label>
                    <select id="kategori" name="Kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori Kendaraan --</option>
                        <option value="Motor Matic">Motor Matic</option>
                        <option value="Motor Bebek">Motor Bebek</option>
                        <option value="Motor Sport">Motor Sport</option>
                        <option value="Mobil Matic">Mobil Matic</option>
                        <option value="Mobil Manual">Mobil Manual</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_cek_km" class="form-label fw-semibold">Interval Cek KM (Opsional)</label>
                    <div class="input-group">
                        <input type="number" id="interval_cek_km" name="Interval_Cek_KM" class="form-control" min="1" placeholder="Kosongkan jika tidak ada jadwal cek">
                        <span class="input-group-text">KM</span>
                    </div>
                    <div class="form-text text-warning-emphasis">Memicu status <strong>Warning Kuning (Perlu Dicek)</strong> saat tercapai.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_ganti_km" class="form-label fw-semibold">Interval Ganti KM (Opsional)</label>
                    <div class="input-group">
                        <input type="number" id="interval_ganti_km" name="Interval_Ganti_KM" class="form-control" min="1" placeholder="Kosongkan jika hanya dicek">
                        <span class="input-group-text">KM</span>
                    </div>
                    <div class="form-text text-danger">Memicu status <strong>Warning Merah (Harus Diganti)</strong> saat tercapai. Minimal isi salah satu (Cek atau Ganti).</div>
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan / Spesifikasi Komponen</label>
                    <textarea id="keterangan" name="Keterangan" class="form-control" rows="3" placeholder="Tuliskan catatan spesifikasi atau keterangan komponen (opsional)"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=komponen" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-dark fw-semibold px-4">Simpan Komponen</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>
