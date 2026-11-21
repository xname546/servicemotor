<?php include __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Ubah Data Master Komponen</h2>
        <p class="text-muted small mb-0">Perbarui kategori kendaraan, interval cek/ganti, atau keterangan master komponen</p>
    </div>
    <a href="index.php?page=komponen" class="btn btn-outline-secondary">&larr; Kembali ke Master Komponen</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Formulir Edit Komponen</h5>
    </div>
    <div class="card-body p-4">
        <form action="index.php?page=komponen&amp;action=update" method="post">
            <input type="hidden" name="ID_Komponen" value="<?= (int)$komponen['ID_Komponen'] ?>">

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label for="kode_komponen" class="form-label fw-semibold">Kode Komponen</label>
                    <input type="text" id="kode_komponen" name="Kode_Komponen" class="form-control" value="<?= htmlspecialchars($komponen['Kode_Komponen']) ?>" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="nama_komponen" class="form-label fw-semibold">Nama Komponen</label>
                    <input type="text" id="nama_komponen" name="Nama_Komponen" class="form-control" value="<?= htmlspecialchars($komponen['Nama_Komponen']) ?>" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="kategori" class="form-label fw-semibold">Kategori Kendaraan</label>
                    <select id="kategori" name="Kategori" class="form-select" required>
                        <?php
                        $kategoriOptions = ['Motor Matic', 'Motor Bebek', 'Motor Sport', 'Mobil Matic', 'Mobil Manual'];
                        foreach ($kategoriOptions as $kat):
                            $selected = ($komponen['Kategori'] === $kat) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($kat) ?>" <?= $selected ?>><?= htmlspecialchars($kat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_cek_km" class="form-label fw-semibold">Interval Cek KM (Opsional)</label>
                    <div class="input-group">
                        <input type="number" id="interval_cek_km" name="Interval_Cek_KM" class="form-control" min="1" value="<?= $komponen['Interval_Cek_KM'] !== null ? (int)$komponen['Interval_Cek_KM'] : '' ?>" placeholder="Kosongkan jika tidak ada jadwal cek">
                        <span class="input-group-text">KM</span>
                    </div>
                    <div class="form-text text-warning-emphasis">Memicu status <strong>Warning Kuning (Perlu Dicek)</strong> saat tercapai.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_ganti_km" class="form-label fw-semibold">Interval Ganti KM (Opsional)</label>
                    <div class="input-group">
                        <input type="number" id="interval_ganti_km" name="Interval_Ganti_KM" class="form-control" min="1" value="<?= $komponen['Interval_Ganti_KM'] !== null ? (int)$komponen['Interval_Ganti_KM'] : '' ?>" placeholder="Kosongkan jika hanya dicek">
                        <span class="input-group-text">KM</span>
                    </div>
                    <div class="form-text text-danger">Memicu status <strong>Warning Merah (Harus Diganti)</strong> saat tercapai. Minimal isi salah satu (Cek atau Ganti).</div>
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan / Spesifikasi Komponen</label>
                    <textarea id="keterangan" name="Keterangan" class="form-control" rows="3"><?= htmlspecialchars($komponen['Keterangan'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=komponen" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-dark fw-semibold px-4">Perbarui Komponen</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>
