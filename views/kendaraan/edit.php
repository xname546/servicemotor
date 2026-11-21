<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Ubah Profil Kendaraan</h2>
        <p class="text-muted small mb-0">Perbarui informasi identitas, kategori jenis kendaraan, atau posisi odometer kendaraan Anda</p>
    </div>
    <a href="index.php?page=dashboard" class="btn btn-outline-secondary">&larr; Kembali ke Dashboard</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Formulir Profil Kendaraan</h5>
    </div>
    <div class="card-body p-4">
        <form action="index.php?page=kendaraan&amp;action=update" method="post">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label for="nama_kendaraan" class="form-label fw-semibold">Nama Kendaraan</label>
                    <input type="text" id="nama_kendaraan" name="Nama_Kendaraan" class="form-control" value="<?= htmlspecialchars($kendaraan['Nama_Kendaraan']) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="jenis_kendaraan" class="form-label fw-semibold">Kategori Jenis Kendaraan</label>
                    <select id="jenis_kendaraan" name="Jenis_Kendaraan" class="form-select" required>
                        <?php
                        $jenisOptions = ['Motor Matic', 'Motor Bebek', 'Motor Sport', 'Mobil Matic', 'Mobil Manual'];
                        foreach ($jenisOptions as $opt):
                            $selected = ($kendaraan['Jenis_Kendaraan'] === $opt) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $selected ?>><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="nomor_polisi" class="form-label fw-semibold">Nomor Polisi</label>
                    <input type="text" id="nomor_polisi" name="Nomor_Polisi" class="form-control" value="<?= htmlspecialchars($kendaraan['Nomor_Polisi']) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="km_aktual" class="form-label fw-semibold">KM Aktual Saat Ini</label>
                    <div class="input-group">
                        <input type="number" id="km_aktual" name="KM_Aktual" class="form-control" min="0" value="<?= (int)$kendaraan['KM_Aktual'] ?>" required>
                        <span class="input-group-text">KM</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=dashboard" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
