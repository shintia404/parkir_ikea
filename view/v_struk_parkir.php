<?php
/*
 * Data disiapkan oleh controller (c_transaksi.php, aksi=cetak_struk):
 * $struk -> object hasil M_Transaksi::tampil_struk_by_id()
 *
 * Struk hanya boleh diakses untuk transaksi yang kendaraannya sudah
 * keluar (sudah dipastikan oleh controller sebelum include file ini).
 */

if (!isset($struk) || !$struk) {
    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}

$no_struk     = 'TRX' . str_pad($struk->id_parkir, 3, '0', STR_PAD_LEFT);
$plat_nomor   = $struk->plat_nomor ?? '-';
$jenis        = $struk->jenis_kendaraan ? ucfirst($struk->jenis_kendaraan) : '-';
$area         = $struk->nama_area ?? '-';

$ts_masuk  = !empty($struk->waktu_masuk)  ? strtotime($struk->waktu_masuk)  : null;
$ts_keluar = !empty($struk->waktu_keluar) ? strtotime($struk->waktu_keluar) : null;

$durasi       = $struk->durasi_jam ? $struk->durasi_jam . ' Jam' : '-';
$tarif_jam    = 'Rp' . number_format((float) ($struk->tarif_per_jam ?? 0), 0, ',', '.') . '/Jam';
$biaya_total  = 'Rp' . number_format((float) ($struk->biaya_total ?? 0), 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Struk Parkir - Parkir IKEA</title>

    <link rel="stylesheet" href="../assets/struk_parkir.css">
</head>

<body>

<div class="struk-container">

    <div class="struk">

        <!-- HEADER -->
        <div class="header">
            <div class="logo">P</div>
            <h1>PARKIR IKEA</h1>
            <p>BUKTI PEMBAYARAN PARKIR</p>
        </div>

        <div class="garis"></div>

        <!-- INFORMASI STRUK -->
        <div class="info">

            <div class="row">
                <span>No. Struk</span>
                <strong><?= htmlspecialchars($no_struk); ?></strong>
            </div>

            <div class="row">
                <span>No. Polisi</span>
                <strong><?= htmlspecialchars($plat_nomor); ?></strong>
            </div>

            <div class="row">
                <span>Kendaraan</span>
                <strong><?= htmlspecialchars($jenis); ?></strong>
            </div>

            <div class="row">
                <span>Area Parkir</span>
                <strong><?= htmlspecialchars($area); ?></strong>
            </div>

        </div>

        <div class="garis"></div>

        <!-- WAKTU -->
        <div class="waktu">

            <div class="waktu-item">
                <span>Waktu Masuk</span>
                <strong><?= $ts_masuk ? date('d/m/Y', $ts_masuk) : '-'; ?></strong>
                <small><?= $ts_masuk ? date('H:i', $ts_masuk) . ' WIB' : ''; ?></small>
            </div>

            <div class="waktu-item">
                <span>Waktu Keluar</span>
                <strong><?= $ts_keluar ? date('d/m/Y', $ts_keluar) : '-'; ?></strong>
                <small><?= $ts_keluar ? date('H:i', $ts_keluar) . ' WIB' : ''; ?></small>
            </div>

        </div>

        <div class="garis"></div>

        <!-- BIAYA -->
        <div class="biaya">

            <div class="row">
                <span>Durasi</span>
                <strong><?= htmlspecialchars($durasi); ?></strong>
            </div>

            <div class="row">
                <span>Tarif</span>
                <strong><?= htmlspecialchars($tarif_jam); ?></strong>
            </div>

            <div class="total">
                <span>TOTAL</span>
                <strong><?= htmlspecialchars($biaya_total); ?></strong>
            </div>

        </div>

        <div class="garis"></div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Terima kasih telah menggunakan</p>
            <strong>LAYANAN PARKIR IKEA 💗</strong>
            <small>Simpan struk ini sebagai bukti parkir.</small>
        </div>

    </div>

    <!-- BUTTON -->
    <div class="aksi">
        <button onclick="window.print()" class="btn-cetak">🖨 Cetak Struk</button>

        <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-kembali">← Kembali</a>
    </div>

</div>

</body>
</html>
