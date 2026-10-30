<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini hanya boleh diakses setelah login
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit;
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
                    <h2>150</h2>
                </div>
            </div>

            <div class="card purple">
                <div class="card-icon">🏍️</div>
                <div>
                    <span>Total Motor</span>
                    <h2>95</h2>
                </div>
            </div>

            <div class="card green">
                <div class="card-icon">🚘</div>
                <div>
                    <span>Total Mobil</span>
                    <h2>55</h2>
                </div>
            </div>

            <div class="card yellow">
                <div class="card-icon">💰</div>
                <div>
                    <span>Total Pendapatan</span>
                    <h2>Rp450K</h2>
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

                <a href="../view/v_laporan.php" class="small-button">
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

                <a href="../view/v_riwayat.php" class="small-button">
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

                        <tr>
                            <td>1</td>
                            <td>D 1234 ABC</td>
                            <td>Motor</td>
                            <td>08:15</td>
                            <td>10:30</td>
                            <td>
                                <span class="status selesai">
                                    Selesai
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>D 5678 XYZ</td>
                            <td>Mobil</td>
                            <td>09:00</td>
                            <td>12:00</td>
                            <td>
                                <span class="status selesai">
                                    Selesai
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>D 2222 KLM</td>
                            <td>Motor</td>
                            <td>10:20</td>
                            <td>13:15</td>
                            <td>
                                <span class="status selesai">
                                    Selesai
                                </span>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>
