<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Catat Transaksi Servis Baru</h2>
        <p class="text-muted small mb-0">Isi informasi utama kunjungan servis &amp; foto nota, lalu tambahkan komponen yang diganti/dicek pada halaman detail</p>
    </div>
    <a href="index.php?page=service" class="btn btn-outline-secondary">&larr; Kembali ke Riwayat Servis</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Formulir Header Servis</h5>
    </div>
    <div class="card-body p-4">
        <form action="index.php?page=service&amp;action=store" method="post" enctype="multipart/form-data">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label for="tanggal_service" class="form-label fw-semibold">Tanggal Servis</label>
                    <input type="date" id="tanggal_service" name="Tanggal_Service" class="form-control" value="<?= htmlspecialchars($todayDate ?? date('Y-m-d')) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="km_service" class="form-label fw-semibold">KM Odometer Saat Servis</label>
                    <div class="input-group">
                        <input type="number" id="km_service" name="KM_Service" class="form-control" min="0" value="<?= (int)$kendaraan['KM_Aktual'] ?>" required>
                        <span class="input-group-text">KM</span>
                    </div>
                    <div class="form-text">
                        KM Aktual kendaraan saat ini: <strong><?= formatKM($kendaraan['KM_Aktual']) ?></strong>. Boleh diisi lebih rendah untuk mencatat servis lama (KM Aktual hanya diperbarui jika KM servis lebih besar).
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="lokasi_bengkel" class="form-label fw-semibold">Nama / Lokasi Bengkel</label>
                    <input type="text" id="lokasi_bengkel" name="Lokasi_Bengkel" class="form-control" placeholder="Contoh: AHASS Tebet" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="biaya_jasa" class="form-label fw-semibold">Biaya Jasa Servis</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="biaya_jasa" name="Biaya_Jasa" class="form-control" min="0" value="0" required>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="file_nota" class="form-label fw-semibold">Upload Foto Nota Servis (Opsional)</label>
                    <input type="file" id="file_nota" name="file_nota" class="form-control" accept="image/*">
                    <div class="form-text">Format gambar: JPG, PNG, atau WEBP.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="catatan_service" class="form-label fw-semibold">Catatan Servis (Opsional)</label>
                    <textarea id="catatan_service" name="Catatan_Service" class="form-control" rows="2" placeholder="Tuliskan keluhan atau catatan pengerjaan servis"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=service" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold px-4">Simpan &amp; Pilih Komponen Servis &rarr;</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
