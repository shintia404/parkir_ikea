
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini dibuka lewat controller supaya datanya diambil dari database
// dan filternya benar-benar diproses. Kalau dibuka langsung (mis. bookmark
// lama), arahkan ke controller.
if (!isset($data_laporan)) {
    header('Location: ../controller/c_laporan.php');
    exit();
}

$link_dashboard = (($_SESSION['role'] ?? '') === 'owner')
    ? 'DashBoard_Owner.php'
    : 'DashBoard.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Parkir</title>

    <link rel="stylesheet" href="../assets/laporan.css">
    
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div>
            <h1>♡ Laporan Parkir</h1>
            <p>Ringkasan data dan aktivitas parkir</p>
        </div>

        <a href="../view/<?= $link_dashboard ?>" class="btn-kembali">
            ← Kembali
        </a>

    </div>


    <!-- FILTER -->
    <div class="filter-card">

        <h2>🔎 Filter Laporan</h2>

        <form method="get" action="../controller/c_laporan.php">

            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>">
            </div>

            <div class="form-group">
                <label>Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>">
            </div>

            <div class="form-group">
                <label>Jenis Kendaraan</label>

                <select name="jenis_kendaraan">
                    <option value=""      <?= $jenis === ''      ? 'selected' : '' ?>>Semua Kendaraan</option>
                    <option value="Motor" <?= $jenis === 'Motor' ? 'selected' : '' ?>>Motor</option>
                    <option value="Mobil" <?= $jenis === 'Mobil' ? 'selected' : '' ?>>Mobil</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">
                Tampilkan
            </button>

        </form>

    </div>


    <!-- RINGKASAN -->
    <div class="summary">

        <div class="summary-card">
            <div class="icon">🚗</div>
            <div>
                <h3><?= number_format($total_kendaraan, 0, ',', '.') ?></h3>
                <p>Total Kendaraan</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">🏍️</div>
            <div>
                <h3><?= number_format($total_motor, 0, ',', '.') ?></h3>
                <p>Total Motor</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">🚘</div>
            <div>
                <h3><?= number_format($total_mobil, 0, ',', '.') ?></h3>
                <p>Total Mobil</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">💰</div>
            <div>
                <h3>Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>

    </div>


    <!-- LAPORAN -->
    <div class="card">

        <div class="card-header">

            <div>
                <h2>📊 Data Laporan</h2>
                <p>Daftar transaksi parkir berdasarkan periode</p>
            </div>

            <button class="btn-cetak" onclick="window.print()">
                🖨 Cetak Laporan
            </button>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No. Kendaraan</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Durasi</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($data_laporan)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data_laporan as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= !empty($row->waktu_keluar) ? date('d-m-Y', strtotime($row->waktu_keluar)) : '-' ?></td>
                        <td>
                            <span class="plat"><?= htmlspecialchars($row->plat_nomor ?? '-') ?></span>
                        </td>
                        <td><?= htmlspecialchars(ucfirst($row->jenis_kendaraan ?? '-')) ?></td>
                        <td><?= htmlspecialchars($row->nama_area ?? '-') ?></td>
                        <td><?= !empty($row->waktu_masuk)  ? date('H:i', strtotime($row->waktu_masuk))  : '-' ?></td>
                        <td><?= !empty($row->waktu_keluar) ? date('H:i', strtotime($row->waktu_keluar)) : '-' ?></td>
                        <td><?= !empty($row->durasi_jam) ? (int) $row->durasi_jam . ' Jam' : '-' ?></td>
                        <td>
                            <span class="harga">Rp<?= number_format((float) ($row->biaya_total ?? 0), 0, ',', '.') ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;color:#aaa;padding:24px;">
                            Tidak ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>
