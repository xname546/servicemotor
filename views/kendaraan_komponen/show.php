<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary"><?= htmlspecialchars($komponen['Kode_Komponen']) ?></span>
            <span class="badge bg-dark"><?= htmlspecialchars($komponen['Kategori']) ?></span>
        </div>
        <h2 class="h4 fw-bold mb-0">Riwayat Komponen: <?= htmlspecialchars($komponen['Nama_Komponen']) ?></h2>
    </div>
    <a href="index.php?page=kendaraan_komponen" class="btn btn-outline-secondary">&larr; Kembali</a>
</div>

<!-- Kartu Ringkasan Komponen (Di Atas Tabel) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 bg-light rounded border h-100">
                    <div class="text-muted small fw-semibold mb-1">Keterangan Komponen</div>
                    <div class="fw-semibold"><?= htmlspecialchars($komponen['Keterangan'] ?: 'Tidak ada keterangan tambahan') ?></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 bg-light rounded border h-100">
                    <div class="text-muted small fw-semibold mb-1">Interval Rekomendasi</div>
                    <div class="fw-bold text-primary fs-6"><?= htmlspecialchars(formatIntervalKomponen($komponen)) ?></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded h-100">
                    <div class="text-muted small fw-semibold mb-1">Rata-rata Interval / Selisih KM Aktual</div>
                    <?php if ($rataRataSelisihKm !== null): ?>
                        <div class="fw-bold text-primary fs-5"><?= formatKM($rataRataSelisihKm) ?></div>
                    <?php else: ?>
                        <div class="text-muted small fw-semibold">Minimal 2 riwayat servis</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Penggantian & Pengecekan -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold fs-6">Tabel Riwayat Penggantian &amp; Pengecekan</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Tanggal Servis</th>
                        <th>Tindakan</th>
                        <th>KM Saat Servis</th>
                        <th>Interval / Selisih KM</th>
                        <th>Lokasi Bengkel</th>
                        <th>Biaya</th>
                        <th>Keterangan</th>
                        <th class="pe-3 text-center">Servis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayatList)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Belum ada riwayat penggantian atau pengecekan untuk komponen ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayatList as $idx => $rw): ?>
                            <tr>
                                <td class="ps-3"><?= $idx + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($rw['Tanggal_Service']) ?></td>
                                <td>
                                    <?php if (($rw['Tindakan'] ?? 'Ganti') === 'Ganti'): ?>
                                        <span class="badge bg-primary">Ganti</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Cek</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= formatKM($rw['KM_Service']) ?></td>
                                <td>
                                    <?php if ($rw['Selisih_KM'] !== null): ?>
                                        <div class="fw-bold text-success">+<?= formatKM($rw['Selisih_KM']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($rw['Acuan_Label']) ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($rw['Acuan_Label']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($rw['Lokasi_Bengkel'] ?: '-') ?></td>
                                <td class="fw-semibold"><?= formatRupiah($rw['Subtotal_Harga']) ?></td>
                                <td><?= htmlspecialchars($rw['Keterangan'] ?: '-') ?></td>
                                <td class="pe-3 text-center">
                                    <a href="index.php?page=service&amp;action=show&amp;id=<?= (int)$rw['ID_Service'] ?>" class="btn btn-sm btn-outline-primary">
                                        #<?= (int)$rw['ID_Service'] ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
