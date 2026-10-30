<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini hanya boleh diakses setelah login
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// --- AMBIL DATA ASLI DARI DATABASE ---
// Sebelumnya kartu ringkasan & tabel "Riwayat Terbaru" di bawah ini masih
// hardcode (angka & baris contoh yang tidak berubah). Sekarang dihitung
// dari transaksi_parkir yang berstatus "keluar", memakai model yang sama
// dipakai halaman Riwayat (m_transaksi.php::tampil_riwayat()).
require_once __DIR__ . '/../models/m_transaksi.php';

$koneksi_obj     = new koneksi();
$transaksi_model = new M_Transaksi($koneksi_obj->koneksi);
$riwayat_semua   = $transaksi_model->tampil_riwayat(); // sudah urut terbaru dulu

$total_kendaraan  = count($riwayat_semua);
$total_motor      = 0;
$total_mobil      = 0;
$total_pendapatan = 0;

foreach ($riwayat_semua as $r) {
    $jenis_r = strtolower($r->jenis_kendaraan ?? '');
    if ($jenis_r === 'motor') {
        $total_motor++;
    } elseif ($jenis_r === 'mobil') {
        $total_mobil++;
    }
    $total_pendapatan += (int) ($r->biaya_total ?? 0);
}

$riwayat_terbaru = array_slice($riwayat_semua, 0, 5);

function safe_owner(mixed $v, string $fb = '-'): string {
    return htmlspecialchars($v ?? $fb, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Owner - ParkirKu</title>

    <link rel="stylesheet" href="../assets/DashBoard.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

<div class="wrapper">

    <?php include __DIR__ . '/navbar.php'; ?>

    <!-- MAIN -->
    <main class="main">

        <!-- HEADER -->
        <header class="header">

            <div>
                <h1>Dashboard Owner 🌷</h1>

                <p>
                    Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?>!
                </p>
            </div>

            <div class="profile">
                <div class="avatar">
                    <?= htmlspecialchars(strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1))) ?>
                </div>

                <div>
                    <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?></strong>
                    <small><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? '')) ?></small>
                </div>
            </div>

        </header>


        <!-- REKAP RIWAYAT (RINGKASAN) -->
        <section class="stats">

            <div class="card pink">
                <div class="card-icon">🚗</div>
                <div>
                    <span>Total Kendaraan</span>
                    <h2><?= number_format($total_kendaraan, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="card purple">
                <div class="card-icon">🏍️</div>
                <div>
                    <span>Total Motor</span>
                    <h2><?= number_format($total_motor, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="card green">
                <div class="card-icon">🚘</div>
                <div>
                    <span>Total Mobil</span>
                    <h2><?= number_format($total_mobil, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="card yellow">
                <div class="card-icon">💰</div>
                <div>
                    <span>Total Pendapatan</span>
                    <h2>Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                </div>
            </div>

        </section>


        <!-- REKAP RIWAYAT -->
        <section class="content-card">

            <div class="section-title">

                <div>
                    <h2>📊 Rekap Riwayat</h2>

                    <p>
                        Ringkasan data parkir berdasarkan periode
                    </p>
                </div>

                <a href="../controller/c_laporan.php" class="small-button">
                    Lihat Rekap Lengkap
                </a>

            </div>

            <p style="color:#aaa; font-size:13px;">
                Buka Rekap Riwayat untuk memfilter berdasarkan tanggal dan jenis kendaraan, lalu cetak laporannya.
            </p>

        </section>


        <!-- RIWAYAT TERBARU -->
        <section class="content-card">

            <div class="section-title">

                <div>
                    <h2>📋 Riwayat Terbaru</h2>

                    <p>
                        Aktivitas parkir yang baru selesai
                    </p>
                </div>

                <a href="../controller/c_riwayat.php" class="small-button">
                    Lihat Semua
                </a>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Kendaraan</th>
                            <th>Jenis</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($riwayat_terbaru)): ?>
                        <?php $no_owner = 1; ?>
                        <?php foreach ($riwayat_terbaru as $rt): ?>
                        <tr>
                            <td><?= $no_owner++ ?></td>
                            <td><span class="plat"><?= safe_owner($rt->plat_nomor) ?></span></td>
                            <td><?= safe_owner(ucfirst($rt->jenis_kendaraan ?? '')) ?></td>
                            <td><?= !empty($rt->waktu_masuk)  ? date('H:i', strtotime($rt->waktu_masuk))  : '-' ?></td>
                            <td><?= !empty($rt->waktu_keluar) ? date('H:i', strtotime($rt->waktu_keluar)) : '-' ?></td>
                            <td>
                                <span class="status selesai">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:#aaa;padding:24px;">
                                Belum ada riwayat kendaraan yang keluar.
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>
