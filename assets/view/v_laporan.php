
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

        <form>

            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai">
            </div>

            <div class="form-group">
                <label>Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir">
            </div>

            <div class="form-group">
                <label>Jenis Kendaraan</label>

                <select name="jenis_kendaraan">
                    <option value="">Semua Kendaraan</option>
                    <option value="Motor">Motor</option>
                    <option value="Mobil">Mobil</option>
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
                <h3>150</h3>
                <p>Total Kendaraan</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">🏍️</div>
            <div>
                <h3>95</h3>
                <p>Total Motor</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">🚘</div>
            <div>
                <h3>55</h3>
                <p>Total Mobil</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon">💰</div>
            <div>
                <h3>Rp450.000</h3>
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

                    <tr>
                        <td>1</td>
                        <td>04-09-2026</td>
                        <td>
                            <span class="plat">D 1234 ABC</span>
                        </td>
                        <td>Motor</td>
                        <td>A-01</td>
                        <td>08:15</td>
                        <td>10:30</td>
                        <td>2 Jam 15 Menit</td>
                        <td>
                            <span class="harga">Rp5.000</span>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>04-09-2026</td>
                        <td>
                            <span class="plat">D 5678 XYZ</span>
                        </td>
                        <td>Mobil</td>
                        <td>B-03</td>
                        <td>09:00</td>
                        <td>12:00</td>
                        <td>3 Jam</td>
                        <td>
                            <span class="harga">Rp10.000</span>
                        </td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>04-09-2026</td>
                        <td>
                            <span class="plat">D 2222 KLM</span>
                        </td>
                        <td>Motor</td>
                        <td>A-05</td>
                        <td>10:20</td>
                        <td>13:15</td>
                        <td>2 Jam 55 Menit</td>
                        <td>
                            <span class="harga">Rp5.000</span>
                        </td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td>04-09-2026</td>
                        <td>
                            <span class="plat">D 9999 PQR</span>
                        </td>
                        <td>Mobil</td>
                        <td>B-02</td>
                        <td>11:00</td>
                        <td>15:30</td>
                        <td>4 Jam 30 Menit</td>
                        <td>
                            <span class="harga">Rp15.000</span>
                        </td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>04-09-2026</td>
                        <td>
                            <span class="plat">D 3456 DEF</span>
                        </td>
                        <td>Motor</td>
                        <td>A-08</td>
                        <td>13:10</td>
                        <td>15:00</td>
                        <td>1 Jam 50 Menit</td>
                        <td>
                            <span class="harga">Rp5.000</span>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>

