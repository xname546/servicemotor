<?php

class KendaraanKomponenController
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

        // 1. Ambil daftar komponen yang dipantau pada kendaraan aktif
        $stmt = $pdo->prepare("
            SELECT kk.*, k.`Kode_Komponen`, k.`Nama_Komponen`, k.`Kategori`,
                   k.`Interval_Cek_KM`, k.`Interval_Ganti_KM`, k.`Keterangan`
            FROM `Kendaraan_Komponen` kk
            JOIN `Komponen` k ON kk.`ID_Komponen` = k.`ID_Komponen`
            WHERE kk.`ID_Kendaraan` = :id
            ORDER BY k.`Kode_Komponen` ASC
        ");
        $stmt->execute(['id' => $idKendaraan]);
        $rows = $stmt->fetchAll();

        // 2. Hitung statistik jumlah riwayat servis (Total_Riwayat, Jumlah_Ganti, Jumlah_Cek) per komponen
        $stmtStat = $pdo->prepare("
            SELECT sk.`ID_Komponen`,
                   COUNT(*) AS total_riwayat,
                   SUM(CASE WHEN sk.`Tindakan` = 'Ganti' THEN 1 ELSE 0 END) AS jumlah_ganti,
                   SUM(CASE WHEN sk.`Tindakan` = 'Cek' THEN 1 ELSE 0 END) AS jumlah_cek
            FROM `Service_Komponen` sk
            JOIN `Service` s ON sk.`ID_Service` = s.`ID_Service`
            WHERE s.`ID_Kendaraan` = :id_kendaraan
            GROUP BY sk.`ID_Komponen`
        ");
        $stmtStat->execute(['id_kendaraan' => $idKendaraan]);
        $statsMap = [];
        foreach ($stmtStat->fetchAll() as $st) {
            $statsMap[(int)$st['ID_Komponen']] = [
                'Total_Riwayat' => (int)$st['total_riwayat'],
                'Jumlah_Ganti' => (int)$st['jumlah_ganti'],
                'Jumlah_Cek' => (int)$st['jumlah_cek'],
            ];
        }

        $komponenKendaraanList = [];
        foreach ($rows as $row) {
            $idKomp = (int)$row['ID_Komponen'];
            $stat = $statsMap[$idKomp] ?? ['Total_Riwayat' => 0, 'Jumlah_Ganti' => 0, 'Jumlah_Cek' => 0];
            $row['Total_Riwayat'] = $stat['Total_Riwayat'];
            $row['Jumlah_Ganti'] = $stat['Jumlah_Ganti'];
            $row['Jumlah_Cek'] = $stat['Jumlah_Cek'];
            $komponenKendaraanList[] = $row;
        }

        require __DIR__ . '/../views/kendaraan_komponen/index.php';
    }

    public function create(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        $stmtKend = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmtKend->execute(['id' => $idKendaraan]);
        $kendaraan = $stmtKend->fetch();

        if (!$kendaraan) {
            header('Location: index.php?page=logout');
            exit;
        }

        // Ambil komponen master yang sesuai Jenis_Kendaraan dan belum terdaftar di kendaraan ini
        $stmt = $pdo->prepare("
            SELECT * FROM `Komponen`
            WHERE `Kategori` = :kategori
              AND `ID_Komponen` NOT IN (
                  SELECT `ID_Komponen` FROM `Kendaraan_Komponen` WHERE `ID_Kendaraan` = :id
              )
            ORDER BY `Kode_Komponen` ASC
        ");
        $stmt->execute([
            'kategori' => $kendaraan['Jenis_Kendaraan'],
            'id' => $idKendaraan,
        ]);
        $masterKomponenList = $stmt->fetchAll();

        require __DIR__ . '/../views/kendaraan_komponen/create.php';
    }

    public function store(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idKomponen = (int)($_POST['ID_Komponen'] ?? 0);

        if ($idKomponen <= 0) {
            setFlash('danger', 'Silakan pilih komponen terlebih dahulu.');
            header('Location: index.php?page=kendaraan_komponen&action=create');
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                INSERT INTO `Kendaraan_Komponen` (
                    `ID_Kendaraan`, `ID_Komponen`,
                    `KM_Terakhir_Cek`, `Tanggal_Terakhir_Cek`,
                    `KM_Terakhir_Ganti`, `Tanggal_Terakhir_Ganti`
                ) VALUES (:id_kendaraan, :id_komponen, NULL, NULL, NULL, NULL)
            ");
            $stmt->execute([
                'id_kendaraan' => $idKendaraan,
                'id_komponen' => $idKomponen,
            ]);

            setFlash('success', 'Komponen berhasil ditambahkan ke daftar pantau kendaraan Anda.');
            header('Location: index.php?page=kendaraan_komponen');
            exit;
        } catch (PDOException $e) {
            setFlash('danger', 'Komponen tersebut sudah terdaftar pada kendaraan ini.');
            header('Location: index.php?page=kendaraan_komponen');
            exit;
        }
    }

    public function show(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idKk = (int)($_GET['id'] ?? 0);

        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT kk.*, k.`Kode_Komponen`, k.`Nama_Komponen`, k.`Kategori`,
                   k.`Interval_Cek_KM`, k.`Interval_Ganti_KM`, k.`Keterangan`
            FROM `Kendaraan_Komponen` kk
            JOIN `Komponen` k ON kk.`ID_Komponen` = k.`ID_Komponen`
            WHERE kk.`ID_Kendaraan_Komponen` = :id_kk AND kk.`ID_Kendaraan` = :id_kendaraan
        ");
        $stmt->execute([
            'id_kk' => $idKk,
            'id_kendaraan' => $idKendaraan,
        ]);
        $komponenKendaraan = $stmt->fetch();

        if (!$komponenKendaraan) {
            setFlash('danger', 'Data komponen kendaraan tidak ditemukan.');
            header('Location: index.php?page=kendaraan_komponen');
            exit;
        }

        $komponen = [
            'ID_Komponen' => $komponenKendaraan['ID_Komponen'],
            'Kode_Komponen' => $komponenKendaraan['Kode_Komponen'],
            'Nama_Komponen' => $komponenKendaraan['Nama_Komponen'],
            'Kategori' => $komponenKendaraan['Kategori'],
            'Interval_Cek_KM' => $komponenKendaraan['Interval_Cek_KM'],
            'Interval_Ganti_KM' => $komponenKendaraan['Interval_Ganti_KM'],
            'Keterangan' => $komponenKendaraan['Keterangan'],
        ];

        // Ambil seluruh riwayat servis untuk komponen ini pada kendaraan aktif (kronologis terlama ke terbaru)
        $stmtRiwayat = $pdo->prepare("
            SELECT sk.*, s.`Tanggal_Service`, s.`KM_Service`, s.`Lokasi_Bengkel`
            FROM `Service_Komponen` sk
            JOIN `Service` s ON sk.`ID_Service` = s.`ID_Service`
            WHERE s.`ID_Kendaraan` = :id_kendaraan AND sk.`ID_Komponen` = :id_komponen
            ORDER BY s.`KM_Service` ASC, s.`Tanggal_Service` ASC, s.`ID_Service` ASC
        ");
        $stmtRiwayat->execute([
            'id_kendaraan' => $idKendaraan,
            'id_komponen' => (int)$komponenKendaraan['ID_Komponen'],
        ]);
        $rawList = $stmtRiwayat->fetchAll();

        $prevGantiKm = null;
        $prevAnyKm = null;
        $processed = [];
        $selisihList = [];

        foreach ($rawList as $row) {
            $kmService = (int)$row['KM_Service'];
            $tindakan = $row['Tindakan'] ?? 'Ganti';

            if ($tindakan === 'Ganti') {
                if ($prevGantiKm !== null) {
                    $selisih = $kmService - $prevGantiKm;
                    $acuanLabel = 'dari Ganti di ' . formatKM($prevGantiKm);
                    $selisihList[] = $selisih;
                } else {
                    $selisih = null;
                    $acuanLabel = 'Ganti Pertama';
                }
                $prevGantiKm = $kmService;
                $prevAnyKm = $kmService;
            } else {
                if ($prevAnyKm !== null) {
                    $selisih = $kmService - $prevAnyKm;
                    $acuanLabel = 'dari Cek/Ganti di ' . formatKM($prevAnyKm);
                    $selisihList[] = $selisih;
                } else {
                    $selisih = null;
                    $acuanLabel = 'Cek Pertama';
                }
                $prevAnyKm = $kmService;
            }

            $row['Selisih_KM'] = $selisih;
            $row['Acuan_Label'] = $acuanLabel;
            $processed[] = $row;
        }

        $riwayatList = array_reverse($processed);
        $rataRataSelisihKm = !empty($selisihList)
            ? (int)round(array_sum($selisihList) / count($selisihList))
            : null;

        require __DIR__ . '/../views/kendaraan_komponen/show.php';
    }

    public function delete(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idKk = (int)($_POST['ID_Kendaraan_Komponen'] ?? 0);

        if ($idKk > 0) {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                DELETE FROM `Kendaraan_Komponen`
                WHERE `ID_Kendaraan_Komponen` = :id_kk AND `ID_Kendaraan` = :id_kendaraan
            ");
            $stmt->execute([
                'id_kk' => $idKk,
                'id_kendaraan' => $idKendaraan,
            ]);
            setFlash('success', 'Komponen berhasil dihapus dari kendaraan Anda.');
        }

        header('Location: index.php?page=kendaraan_komponen');
        exit;
    }
}
