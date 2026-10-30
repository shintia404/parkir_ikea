<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini dibuka lewat controller supaya datanya diambil dari database.
// Kalau dibuka langsung (mis. bookmark lama), arahkan ke controller.
if (!isset($data_riwayat)) {
    header('Location: ../controller/c_riwayat.php');
    exit();
}

$role = $_SESSION['role'] ?? '';

$link_dashboard = ($role === 'owner')
    ? 'DashBoard_Owner.php'
    : 'DashBoard.php';

// Tombol struk hanya untuk admin/petugas (owner tidak punya akses ke c_transaksi)
$bisa_struk = in_array($role, ['admin', 'petugas'], true);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Parkir</title>

    <link rel="stylesheet" href="../assets/riwayat.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>♡ Riwayat Parkir</h1>
                <p>Daftar riwayat kendaraan yang telah melakukan parkir</p>
            </div>

            <a href="../view/<?= $link_dashboard ?>" class="btn-kembali">
                ← Kembali
            </a>
        </div>


        <!-- CARD -->
        <div class="card">

            <div class="card-header">
                <h2>📋 Data Riwayat</h2>

                <div class="search-box">
                    <input type="text" placeholder="Cari kendaraan...">
                    <button>🔍</button>
                </div>
            </div>


            <!-- TABLE -->
            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>No. Kendaraan</th>
                            <th>Jenis</th>
                            <th>Area Parkir</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <?php if ($bisa_struk): ?>
                            <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($data_riwayat)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data_riwayat as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>TRX<?= str_pad($row->id_parkir, 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <span class="plat"><?= htmlspecialchars($row->plat_nomor ?? '-') ?></span>
                            </td>
                            <td><?= htmlspecialchars(ucfirst($row->jenis_kendaraan ?? '-')) ?></td>
                            <td><?= htmlspecialchars($row->nama_area ?? '-') ?></td>
                            <td><?= !empty($row->waktu_masuk)  ? date('d/m/Y H:i', strtotime($row->waktu_masuk))  : '-' ?></td>
                            <td><?= !empty($row->waktu_keluar) ? date('d/m/Y H:i', strtotime($row->waktu_keluar)) : '-' ?></td>
                            <td><?= !empty($row->durasi_jam) ? (int)$row->durasi_jam . ' Jam' : '-' ?></td>
                            <td>
                                <span class="harga">Rp<?= number_format((float)($row->biaya_total ?? 0), 0, ',', '.') ?></span>
                            </td>
                            <?php if ($bisa_struk): ?>
                            <td>
                                <div class="aksi-group">
                                <?php if ($role === 'petugas'): ?>
                                    <a href="../controller/c_transaksi.php?aksi=keluar_kendaraan&id=<?= $row->id_parkir ?>"
                                       class="btn-aksi btn-cetak" title="Lihat Struk">
                                        🖨️ <span>Lihat Struk</span>
                                    </a>
                                <?php else: ?>
                                    <a href="../controller/c_transaksi.php?aksi=cetak_struk&id=<?= $row->id_parkir ?>"
                                       class="btn-aksi btn-cetak" target="_blank" title="Cetak Struk">
                                        🖨 <span>Cetak</span>
                                    </a>
                                <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $bisa_struk ? 10 : 9 ?>" class="empty-state">
                                Belum ada riwayat kendaraan yang keluar.
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