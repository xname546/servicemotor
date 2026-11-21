<?php include __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Daftar Akun Kendaraan</h2>
        <p class="text-muted small mb-0">Kelola seluruh akun kendaraan yang terdaftar pada sistem beserta posisi odometer aktualnya</p>
    </div>
    <a href="index.php?page=kendaraan&amp;action=create" class="btn btn-dark fw-semibold">+ Tambah Kendaraan Baru</a>
</div>

<div class="row g-3">
    <?php if (empty($kendaraanList)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center text-muted">
                    Belum ada kendaraan terdaftar. Silakan klik tombol <strong>+ Tambah Kendaraan Baru</strong> di sudut kanan atas.
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($kendaraanList as $item): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary"><?= htmlspecialchars($item['Jenis_Kendaraan']) ?></span>
                                <span class="badge bg-dark fs-6"><?= htmlspecialchars($item['Nomor_Polisi']) ?></span>
                            </div>
                            <h3 class="h5 fw-bold mb-1"><?= htmlspecialchars($item['Nama_Kendaraan']) ?></h3>
                        </div>

                        <div class="bg-light rounded p-3 mt-3 border d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Odometer (KM Aktual)</div>
                                <div class="fw-bold text-primary fs-5"><?= formatKM($item['KM_Aktual']) ?></div>
                            </div>
                            <div class="text-end small text-muted">
                                Update Terakhir<br>
                                <strong><?= htmlspecialchars($item['Tgl_Update_KM'] ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>
