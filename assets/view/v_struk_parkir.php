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

$no_struk    = 'TRX' . str_pad($struk->id_parkir, 3, '0', STR_PAD_LEFT);
$plat_nomor  = $struk->plat_nomor ?? '-';
$jenis       = $struk->jenis_kendaraan ? ucfirst($struk->jenis_kendaraan) : '-';
$area        = $struk->nama_area ?? '-';

$ts_masuk  = !empty($struk->waktu_masuk)  ? strtotime($struk->waktu_masuk)  : null;
$ts_keluar = !empty($struk->waktu_keluar) ? strtotime($struk->waktu_keluar) : null;

$tgl_masuk    = $ts_masuk  ? date('d-m-Y', $ts_masuk)  : '-';
$jam_masuk    = $ts_masuk  ? date('H:i', $ts_masuk) . ' WIB' : '-';
$tgl_keluar   = $ts_keluar ? date('d-m-Y', $ts_keluar) : '-';
$jam_keluar   = $ts_keluar ? date('H:i', $ts_keluar) . ' WIB' : '-';

$durasi      = $struk->durasi_jam ? $struk->durasi_jam . ' Jam' : '-';
$tarif_jam   = 'Rp' . number_format((float)($struk->tarif_per_jam ?? 0), 0, ',', '.') . '/Jam';
$biaya_total = 'Rp' . number_format((float)($struk->biaya_total    ?? 0), 0, ',', '.');
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

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div>
            <h1>♡ Struk Parkir</h1>
            <p>Bukti pembayaran parkir kendaraan</p>
        </div>
        <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-kembali">
            ← Kembali
        </a>
    </div>


    <!-- CARD DATA STRUK -->
    <div class="card">

        <div class="card-header">
            <div>
                <h2>📋 Data Struk</h2>
                <p>Detail transaksi parkir</p>
            </div>
            <button class="btn-cetak" onclick="window.print()">
                🖨 Cetak Struk
            </button>
        </div>


        <div class="table-wrapper">
            <table>

                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th>Detail</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>No. Struk</td>
                        <td><span class="plat"><?= htmlspecialchars($no_struk) ?></span></td>
                    </tr>

                    <tr>
                        <td>No. Polisi</td>
                        <td><span class="plat"><?= htmlspecialchars($plat_nomor) ?></span></td>
                    </tr>

                    <tr>
                        <td>Jenis Kendaraan</td>
                        <td><?= htmlspecialchars($jenis) ?></td>
                    </tr>

                    <tr>
                        <td>Area Parkir</td>
                        <td><?= htmlspecialchars($area) ?></td>
                    </tr>

                    <tr>
                        <td>Waktu Masuk</td>
                        <td><?= htmlspecialchars($tgl_masuk) ?> &nbsp; <?= htmlspecialchars($jam_masuk) ?></td>
                    </tr>

                    <tr>
                        <td>Waktu Keluar</td>
                        <td><?= htmlspecialchars($tgl_keluar) ?> &nbsp; <?= htmlspecialchars($jam_keluar) ?></td>
                    </tr>

                    <tr>
                        <td>Durasi</td>
                        <td><?= htmlspecialchars($durasi) ?></td>
                    </tr>

                    <tr>
                        <td>Tarif per Jam</td>
                        <td><span class="harga"><?= htmlspecialchars($tarif_jam) ?></span></td>
                    </tr>

                    <tr class="row-total">
                        <td>Total Biaya</td>
                        <td><span class="badge-total"><?= htmlspecialchars($biaya_total) ?></span></td>
                    </tr>

                </tbody>

            </table>
        </div>

    </div>

</div>

</body>
</html>
