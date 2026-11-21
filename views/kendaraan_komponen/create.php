<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Masukkan Komponen Baru ke Kendaraan</h2>
        <p class="text-muted small mb-0">Menampilkan komponen master yang sesuai dengan kategori kendaraan aktif</p>
    </div>
    <a href="index.php?page=kendaraan_komponen" class="btn btn-outline-secondary">&larr; Kembali ke Komponen Kendaraan</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Pilih Komponen</h5>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-info small mb-4">
            Komponen yang baru ditambahkan akan otomatis berstatus <strong>Belum Ada Riwayat</strong> dan riwayat kilometernya akan diperbarui secara otomatis ketika komponen tersebut dicatat pada transaksi servis.
        </div>

        <form action="index.php?page=kendaraan_komponen&amp;action=store" method="post">
            <div class="mb-4">
                <label for="id_komponen" class="form-label fw-semibold">Pilih Komponen (Kategori: <?= htmlspecialchars($_SESSION['kendaraan_jenis'] ?? '-') ?>)</label>
                <select id="id_komponen" name="ID_Komponen" class="form-select" required>
                    <option value="">-- Pilih Komponen --</option>
                    <?php foreach ($masterKomponenList as $komp): ?>
                        <option value="<?= (int)$komp['ID_Komponen'] ?>">
                            <?= htmlspecialchars($komp['Kode_Komponen']) ?> - <?= htmlspecialchars($komp['Nama_Komponen']) ?> (<?= htmlspecialchars(formatIntervalKomponen($komp)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="index.php?page=kendaraan_komponen" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold px-4">Tambahkan ke Kendaraan</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
