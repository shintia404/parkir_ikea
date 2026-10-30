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
                            <th>No. Kendaraan</th>
                            <th>Jenis</th>
                            <th>Area Parkir</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Durasi</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>1</td>
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