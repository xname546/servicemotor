<?php

class DashboardController
{
    private function getActiveKendaraanId(): int
    {
        if (($_SESSION['role'] ?? '') !== 'kendaraan' || empty($_SESSION['kendaraan_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        return (int)$_SESSION['kendaraan_id'];
    }

    public function index(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        // 1. Ambil profil kendaraan aktif
        $stmt = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmt->execute(['id' => $idKendaraan]);
        $kendaraan = $stmt->fetch();

        if (!$kendaraan) {
            header('Location: index.php?page=logout');
            exit;
        }

        $_SESSION['kendaraan_nama'] = $kendaraan['Nama_Kendaraan'];
        $_SESSION['kendaraan_nopol'] = $kendaraan['Nomor_Polisi'];
        $_SESSION['kendaraan_jenis'] = $kendaraan['Jenis_Kendaraan'];

        // 2. Ambil daftar komponen kendaraan beserta Interval_Cek_KM dan Interval_Ganti_KM
        $stmtKomp = $pdo->prepare("
            SELECT kk.*, k.`Kode_Komponen`, k.`Nama_Komponen`, k.`Kategori`,
                   k.`Interval_Cek_KM`, k.`Interval_Ganti_KM`, k.`Keterangan`
            FROM `Kendaraan_Komponen` kk
            JOIN `Komponen` k ON kk.`ID_Komponen` = k.`ID_Komponen`
            WHERE kk.`ID_Kendaraan` = :id
            ORDER BY k.`Kode_Komponen` ASC
        ");
        $stmtKomp->execute(['id' => $idKendaraan]);
        $rows = $stmtKomp->fetchAll();

        $kmAktual = (int)$kendaraan['KM_Aktual'];
        $komponenEvaluasi = [];

        foreach ($rows as $row) {
            $intervalCek = (int)($row['Interval_Cek_KM'] ?? 0);
            $intervalGanti = (int)($row['Interval_Ganti_KM'] ?? 0);

            $kmTerakhirGanti = $row['KM_Terakhir_Ganti'] !== null ? (int)$row['KM_Terakhir_Ganti'] : null;
            $kmTerakhirCek = $row['KM_Terakhir_Cek'] !== null ? (int)$row['KM_Terakhir_Cek'] : $kmTerakhirGanti;

            $targetKmCek = null;
            $sisaKmCek = null;
            if ($intervalCek > 0 && $kmTerakhirCek !== null) {
                $targetKmCek = $kmTerakhirCek + $intervalCek;
                $sisaKmCek = $targetKmCek - $kmAktual;
            }

            $targetKmGanti = null;
            $sisaKmGanti = null;
            if ($intervalGanti > 0 && $kmTerakhirGanti !== null) {
                $targetKmGanti = $kmTerakhirGanti + $intervalGanti;
                $sisaKmGanti = $targetKmGanti - $kmAktual;
            }

            // Penentuan Status & Prioritas
            if ($targetKmCek === null && $targetKmGanti === null) {
                $status = 'Belum Ada Riwayat';
                $prioritas = 4;
            } elseif ($sisaKmGanti !== null && $sisaKmGanti <= 0) {
                $status = 'Harus Diganti';
                $prioritas = 1;
            } elseif ($sisaKmCek !== null && $sisaKmCek <= 0) {
                $status = 'Perlu Dicek';
                $prioritas = 2;
            } else {
                $status = 'Aman';
                $prioritas = 3;
            }

            $row['KM_Terakhir_Cek_Efektif'] = $kmTerakhirCek;
            $row['Target_KM_Cek'] = $targetKmCek;
            $row['Sisa_KM_Cek'] = $sisaKmCek;
            $row['Target_KM_Ganti'] = $targetKmGanti;
            $row['Sisa_KM'] = $sisaKmGanti;
            $row['Status'] = $status;
            $row['Prioritas'] = $prioritas;

            $komponenEvaluasi[] = $row;
        }

        // Urutkan berdasarkan prioritas warning: Harus Diganti (1) -> Perlu Dicek (2) -> Aman (3) -> Belum Ada Riwayat (4)
        usort($komponenEvaluasi, function (array $a, array $b): int {
            if ($a['Prioritas'] !== $b['Prioritas']) {
                return $a['Prioritas'] <=> $b['Prioritas'];
            }
            return strcmp($a['Kode_Komponen'], $b['Kode_Komponen']);
        });

        require __DIR__ . '/../views/dashboard/index.php';
    }

    public function cekKm(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT `KM_Aktual` FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmt->execute(['id' => $idKendaraan]);
        $kendaraan = $stmt->fetch();

        if (!$kendaraan) {
            header('Location: index.php?page=logout');
            exit;
        }

        $kmLama = (int)$kendaraan['KM_Aktual'];
        $kmBaru = (int)($_POST['km_odometer'] ?? 0);

        if ($kmBaru < $kmLama) {
            setFlash('danger', 'KM Odometer baru (' . formatKM($kmBaru) . ') tidak boleh lebih rendah dari KM Aktual terakhir (' . formatKM($kmLama) . ').');
            header('Location: index.php?page=dashboard');
            exit;
        }

        $upd = $pdo->prepare("
            UPDATE `Kendaraan`
            SET `KM_Aktual` = :km, `Tgl_Update_KM` = CURDATE()
            WHERE `ID_Kendaraan` = :id
        ");
        $upd->execute([
            'km' => $kmBaru,
            'id' => $idKendaraan,
        ]);

        setFlash('success', 'KM Odometer berhasil diperbarui ke ' . formatKM($kmBaru) . '. Berikut hasil pemeriksaan komponen Anda.');
        header('Location: index.php?page=dashboard');
        exit;
    }
}
