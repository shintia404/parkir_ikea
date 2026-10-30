<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Data disiapkan oleh controller (c_transaksi.php, aksi=keluar_kendaraan):
 * $transaksi  -> object detail transaksi (JOIN kendaraan, area, tarif)
 * $data_tarif -> array semua tarif (untuk dropdown pilih tarif)
 */

if (!isset($transaksi) || !$transaksi) {
    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}

if (!isset($data_tarif)) { $data_tarif = []; }

// Jika kendaraan sudah keluar, langsung ke halaman cetak struk
if (strtolower($transaksi->status ?? '') === 'keluar') {
    header('Location: ../controller/c_transaksi.php?aksi=cetak_struk&id=' . $transaksi->id_parkir);
    exit();
}

$no_struk   = 'TRX' . str_pad($transaksi->id_parkir, 3, '0', STR_PAD_LEFT);
$plat_nomor = $transaksi->plat_nomor ?? '-';
$jenis      = $transaksi->jenis_kendaraan ? ucfirst($transaksi->jenis_kendaraan) : '-';
$area       = $transaksi->nama_area ?? '-';
$ts_masuk   = !empty($transaksi->waktu_masuk) ? strtotime($transaksi->waktu_masuk) : null;

$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluar Kendaraan - Parkir IKEA</title>
    <link rel="stylesheet" href="../assets/edit_transaksi.css">
    <link rel="stylesheet" href="../assets/navbar.css">
    <style>
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
            padding: 20px;
            background: #fff5fa;
            border-radius: 12px;
            border: 1px solid #ffe0ec;
        }
        .info-item label {
            display: block;
            font-size: 12px;
            color: #987080;
            margin-bottom: 4px;
            font-weight: normal;
        }
        .info-item strong {
            font-size: 14px;
            color: #5a3d4a;
        }
        .plat-badge {
            display: inline-block;
            background: #fff0f6;
            color: #d94f87;
            padding: 3px 10px;
            border-radius: 7px;
            font-weight: bold;
            font-size: 14px;
        }
        .section-divider {
            font-size: 13px;
            font-weight: bold;
            color: #d94f87;
            margin: 0 0 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ffe0ec;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .alert-error {
            background: #fff0f0;
            border: 1px solid #ffb3b3;
            color: #a03030;
        }
    </style>
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">
    <div class="form-card">

        <!-- HEADER -->
        <div class="header">
            <h1>🚗 Keluar Kendaraan</h1>
            <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-kembali">
                ← Kembali
            </a>
        </div>


        <!-- NOTIFIKASI ERROR (jika ada) -->
        <?php if ($pesan && $pesan['tipe'] === 'error'): ?>
            <div class="alert alert-error">
                <strong><?= htmlspecialchars($pesan['judul']) ?></strong> —
                <?= htmlspecialchars($pesan['teks']) ?>
            </div>
        <?php endif; ?>


        <!-- INFO KENDARAAN -->
        <p class="section-divider">📋 Informasi Kendaraan</p>

        <div class="info-grid">
            <div class="info-item">
                <label>No. Transaksi</label>
                <strong><?= htmlspecialchars($no_struk) ?></strong>
            </div>
            <div class="info-item">
                <label>No. Polisi</label>
                <strong><span class="plat-badge"><?= htmlspecialchars($plat_nomor) ?></span></strong>
            </div>
            <div class="info-item">
                <label>Jenis Kendaraan</label>
                <strong><?= htmlspecialchars($jenis) ?></strong>
            </div>
            <div class="info-item">
                <label>Area Parkir</label>
                <strong><?= htmlspecialchars($area) ?></strong>
            </div>
            <div class="info-item">
                <label>Waktu Masuk</label>
                <strong><?= $ts_masuk ? date('d/m/Y H:i', $ts_masuk) . ' WIB' : '-' ?></strong>
            </div>
            <div class="info-item">
                <label>Status</label>
                <strong>🟡 Masih Parkir</strong>
            </div>
        </div>


        <!-- FORM PROSES KELUAR -->
        <p class="section-divider">⏱ Proses Keluar Kendaraan</p>

        <form action="../controller/c_transaksi.php?aksi=proses_keluar&id=<?= $transaksi->id_parkir ?>"
              method="POST">

            <div class="form-group">
                <label>Waktu Keluar</label>
                <input
                    type="datetime-local"
                    name="waktu_keluar"
                    required
                    min="<?= $ts_masuk ? date('Y-m-d\TH:i', $ts_masuk + 60) : '' ?>"
                >
            </div>

            <div class="form-group">
                <label>Tarif</label>
                <select name="id_tarif" required>
                    <option value="" disabled selected>Pilih Tarif</option>
                    <?php foreach ($data_tarif as $tarif): ?>
                        <option value="<?= $tarif->id_tarif ?>"
                            <?= ((int)($transaksi->id_tarif ?? 0) === (int)$tarif->id_tarif) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($tarif->jenis_kendaraan)) ?>
                            — Rp<?= number_format((float)$tarif->tarif_per_jam, 0, ',', '.') ?>/Jam
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-group">
                <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-batal">
                    Batal
                </a>
                <button type="submit" class="btn-simpan">
                    ✅ Proses Keluar
                </button>
            </div>

        </form>

    </div>
</div>

<script src="../assets/navbar.js"></script>

</body>
</html>
