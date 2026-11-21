<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Detail Transaksi Servis #<?= (int)$service['ID_Service'] ?></h2>
        <p class="text-muted small mb-0">Tanggal: <?= htmlspecialchars($service['Tanggal_Service']) ?> | Odometer: <?= formatKM($service['KM_Service']) ?></p>
    </div>
    <a href="index.php?page=service" class="btn btn-outline-secondary">&larr; Kembali ke Riwayat Servis</a>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Kartu Informasi & Biaya Servis + Kartu Form Tambah Tindakan Komponen -->
    <div class="col-12 col-lg-4">
        <!-- 1. Kartu Informasi & Biaya Servis -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="h6 fw-bold mb-0">Informasi &amp; Biaya Servis</h3>
                <a href="index.php?page=service&amp;action=edit&amp;id=<?= (int)$service['ID_Service'] ?>" class="btn btn-outline-secondary btn-sm">Ubah</a>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Tanggal Servis:</span>
                    <span class="fw-semibold"><?= htmlspecialchars($service['Tanggal_Service']) ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Lokasi Bengkel:</span>
                    <span class="fw-semibold"><?= htmlspecialchars($service['Lokasi_Bengkel'] ?: '-') ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">KM Saat Servis:</span>
                    <span class="fw-bold text-primary"><?= formatKM($service['KM_Service']) ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Catatan:</span>
                    <span class="text-end"><?= htmlspecialchars($service['Catatan_Service'] ?: '-') ?></span>
                </div>

                <?php if (!empty($service['Nota'])): ?>
                    <div class="d-grid mt-3">
                        <a href="<?= htmlspecialchars($service['Nota']) ?>" target="_blank" class="btn btn-outline-success btn-sm fw-semibold">
                            Lihat Foto Nota Servis
                        </a>
                    </div>
                <?php endif; ?>

                <hr class="my-3">
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Biaya Jasa:</span>
                    <span><?= formatRupiah($service['Biaya_Jasa']) ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-3">
                    <span class="text-muted">Total Biaya Komponen:</span>
                    <span><?= formatRupiah($service['Total_Biaya_Komponen']) ?></span>
                </div>
                <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded p-3 d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold text-primary">Total Biaya Servis</span>
                    <strong class="text-primary fs-5"><?= formatRupiah($service['Total_Biaya']) ?></strong>
                </div>
            </div>
        </div>

        <!-- 2. Kartu Form Tambah Komponen Servis (Cek / Ganti) -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0">Tambah Tindakan Komponen (Cek / Ganti)</h3>
            </div>
            <div class="card-body p-4">
                <form action="index.php?page=service_komponen&amp;action=store" method="post">
                    <input type="hidden" name="ID_Service" value="<?= (int)$service['ID_Service'] ?>">

                    <div class="mb-3">
                        <label for="id_komponen" class="form-label fw-semibold small">Pilih Komponen (<?= htmlspecialchars($kendaraan['Jenis_Kendaraan'] ?? '-') ?>)</label>
                        <select id="id_komponen" name="ID_Komponen" class="form-select" required>
                            <option value="">-- Pilih Komponen --</option>
                            <?php foreach ($masterKomponenList as $komp): ?>
                                <option value="<?= (int)$komp['ID_Komponen'] ?>">
                                    <?= htmlspecialchars($komp['Kode_Komponen']) ?> - <?= htmlspecialchars($komp['Nama_Komponen']) ?> (<?= htmlspecialchars(formatIntervalKomponen($komp)) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tindakan" class="form-label fw-semibold small">Kategori Tindakan</label>
                        <select id="tindakan" name="Tindakan" class="form-select" required>
                            <option value="Ganti">Ganti (Reset KM Ganti &amp; KM Cek)</option>
                            <option value="Cek">Cek (Reset KM Cek Saja)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label for="qty" class="form-label fw-semibold small">Qty</label>
                            <input type="number" id="qty" name="Qty" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-8">
                            <label for="harga_satuan" class="form-label fw-semibold small">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" id="harga_satuan" name="Harga_Satuan" class="form-control" min="0" value="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan (Opsional)</label>
                        <input type="text" id="keterangan" name="Keterangan" class="form-control" placeholder="Merek part / kondisi hasil pengecekan">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">+ Simpan Tindakan Komponen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Komponen Servis (Cek / Ganti) -->
    <div class="col-12 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="h5 fw-bold mb-0">Daftar Komponen Servis (Cek / Ganti)</h3>
                <small class="text-muted">Rincian suku cadang yang diganti atau diperiksa pada kunjungan servis ini</small>
            </div>
            <span class="badge bg-secondary"><?= count($detailKomponenList) ?> Item</span>
        </div>

        <div class="row g-3">
            <?php if (empty($detailKomponenList)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center text-muted">
                            Belum ada tindakan komponen yang dicatat pada servis ini.<br>
                            Silakan isi formulir <strong>Tambah Tindakan Komponen (Cek / Ganti)</strong> di panel sebelah kiri.
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($detailKomponenList as $item): ?>
                    <?php $isGanti = (($item['Tindakan'] ?? 'Ganti') === 'Ganti'); ?>
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['Kode_Komponen']) ?></span>
                                            <span class="badge <?= $isGanti ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                                <?= htmlspecialchars($item['Tindakan'] ?? 'Ganti') ?>
                                            </span>
                                        </div>
                                        <div class="fw-bold"><?= htmlspecialchars($item['Nama_Komponen']) ?></div>
                                        <div class="small text-muted">
                                            <?= (int)$item['Qty'] ?> x <?= formatRupiah($item['Harga_Satuan']) ?>
                                            <?php if (!empty($item['Keterangan'])): ?>
                                                | <?= htmlspecialchars($item['Keterangan']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <form action="index.php?page=service_komponen&amp;action=delete" method="post">
                                        <input type="hidden" name="ID_Service_Komponen" value="<?= (int)$item['ID_Service_Komponen'] ?>">
                                        <input type="hidden" name="ID_Service" value="<?= (int)$service['ID_Service'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                    </form>
                                </div>

                                <div class="d-flex justify-content-between align-items-center small pt-2 border-top mt-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <strong class="text-dark"><?= formatRupiah($item['Subtotal_Harga']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
