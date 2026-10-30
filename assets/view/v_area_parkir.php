<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Parkir</title>

   <link rel="stylesheet" href="../assets/area_parkir.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>
<body>

    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>Area Parkir</h1>
                <p>Kelola dan pantau area parkir kendaraan</p>
            </div>

            <a href="DashBoard.php" class="btn-back">
                ← Kembali
            </a>
        </div>

        <!-- INFORMASI PARKIR -->
        <div class="info-container">

            <div class="info-card">
                <div class="icon">🚗</div>
                <div>
                    <span>Total Slot</span>
                    <h2>20</h2>
                </div>
            </div>

            <div class="info-card">
                <div class="icon">🟢</div>
                <div>
                    <span>Slot Tersedia</span>
                    <h2>12</h2>
                </div>
            </div>

            <div class="info-card">
                <div class="icon">🔴</div>
                <div>
                    <span>Slot Terisi</span>
                    <h2>8</h2>
                </div>
            </div>

        </div>

        <!-- AREA PARKIR -->
        <div class="parking-section">

            <div class="section-header">
                <div>
                    <h2>Denah Area Parkir</h2>
                    <p>Klik slot untuk melihat informasi kendaraan</p>
                </div>
            </div>

            <div class="parking-area">

                <!-- SLOT PARKIR -->
                <div class="parking-slot available">
                    <span>A01</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot occupied">
                    <span>A02</span>
                    <small>Terisi</small>
                </div>

                <div class="parking-slot available">
                    <span>A03</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot occupied">
                    <span>A04</span>
                    <small>Terisi</small>
                </div>

                <div class="parking-slot available">
                    <span>A05</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot available">
                    <span>A06</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot occupied">
                    <span>A07</span>
                    <small>Terisi</small>
                </div>

                <div class="parking-slot available">
                    <span>A08</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot available">
                    <span>A09</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot occupied">
                    <span>A10</span>
                    <small>Terisi</small>
                </div>

                <div class="parking-slot available">
                    <span>A11</span>
                    <small>Tersedia</small>
                </div>

                <div class="parking-slot available">
                    <span>A12</span>
                    <small>Tersedia</small>
                </div>

            </div>

            <!-- KETERANGAN -->
            <div class="legend">

                <div class="legend-item">
                    <span class="legend-box available-box"></span>
                    <span>Tersedia</span>
                </div>

                <div class="legend-item">
                    <span class="legend-box occupied-box"></span>
                    <span>Terisi</span>
                </div>

            </div>

        </div>

    </div>

    <script src="../assets/navbar.js"></script>

</body>
</html>