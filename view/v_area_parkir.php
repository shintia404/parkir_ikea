<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan data tersedia dari controller
if (!isset($data_area)) {
    header('Location: ../controller/c_area_parkir.php?aksi=tampil');
    exit();
}

$mode_edit = isset($data_edit) && $data_edit;

// Ambil pesan notifikasi
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);

// Ringkasan slot dihitung dari seluruh data area yang ada
$total_slot     = 0;
$slot_terisi    = 0;
foreach ($data_area as $a) {
    $total_slot  += (int) $a->kapasitas;
    $slot_terisi += (int) $a->terisi;
}
$slot_tersedia = max(0, $total_slot - $slot_terisi);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Area Parkir - Parkir IKEA</title>

    <link rel="stylesheet" href="../assets/area_parkir.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- ================= HEADER ================= -->

    <div class="header">
        <div>
            <h1>🅿️ Area Parkir</h1>
            <p>Kelola dan pantau area parkir kendaraan</p>
        </div>

        <a href="DashBoard.php" class="btn-back">
            ← Kembali
        </a>
    </div>


    <!-- ================= NOTIFIKASI ================= -->

    <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error'; ?>">
            <strong><?= htmlspecialchars($pesan['judul']); ?></strong>
            <span><?= htmlspecialchars($pesan['teks']); ?></span>
        </div>
    <?php endif; ?>


    <!-- ================= INFORMASI PARKIR ================= -->

    <div class="info-container">

        <div class="info-card">
            <div class="icon">🚗</div>
            <div>
                <span>Total Slot</span>
                <h2><?= $total_slot; ?></h2>
            </div>
        </div>

        <div class="info-card">
            <div class="icon">🟢</div>
            <div>
                <span>Slot Tersedia</span>
                <h2><?= $slot_tersedia; ?></h2>
            </div>
        </div>

        <div class="info-card">
            <div class="icon">🔴</div>
            <div>
                <span>Slot Terisi</span>
                <h2><?= $slot_terisi; ?></h2>
            </div>
        </div>

    </div>


    <!-- ================= FORM TAMBAH / EDIT ================= -->

    <div class="card mb-card">
        <div class="card-header">
            <h2><?= $mode_edit ? 'Edit Area Parkir' : 'Tambah Area Parkir'; ?></h2>
        </div>

        <form
            action="../controller/c_area_parkir.php?aksi=<?= $mode_edit ? 'update' : 'tambah'; ?>"
            method="POST"
            class="form-area"
        >
            <input type="hidden" name="id_area" value="<?= htmlspecialchars($data_edit->id_area ?? ''); ?>">

            <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">

                <!-- NAMA AREA -->
                <div class="form-group">
                    <label for="nama_area">Nama Area</label>
                    <input
                        type="text"
                        name="nama_area"
                        id="nama_area"
                        placeholder="Gedung 3"
                        value="<?= htmlspecialchars($data_edit->nama_area ?? ''); ?>"
                        required
                    >
                </div>

                <!-- KAPASITAS -->
                <div class="form-group">
                    <label for="kapasitas">Kapasitas (Total Slot)</label>
                    <input
                        type="number"
                        name="kapasitas"
                        id="kapasitas"
                        placeholder="50"
                        value="<?= htmlspecialchars($data_edit->kapasitas ?? ''); ?>"
                        min="1"
                        required
                    >
                </div>

                <!-- TERISI -->
                <div class="form-group">
                    <label for="terisi">Slot Terisi Saat Ini</label>
                    <input
                        type="number"
                        name="terisi"
                        id="terisi"
                        placeholder="0"
                        value="<?= htmlspecialchars($data_edit->terisi ?? '0'); ?>"
                        min="0"
                    >
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-tambah">
                    <?= $mode_edit ? 'Update' : 'Tambah'; ?>
                </button>

                <?php if ($mode_edit): ?>
                    <a href="../controller/c_area_parkir.php?aksi=tampil" class="btn-batal">
                        Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>


    <!-- ================= DATA AREA PARKIR ================= -->

    <div class="card">
        <div class="card-header">
            <h2>Data Area Parkir</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Area</th>
                        <th>Kapasitas</th>
                        <th>Terisi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!empty($data_area)): ?>
                    <?php $no = 1; foreach ($data_area as $row):
                        $penuh = (int)$row->terisi >= (int)$row->kapasitas;
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row->nama_area); ?></td>
                            <td><?= (int) $row->kapasitas; ?></td>
                            <td><?= (int) $row->terisi; ?></td>
                            <td>
                                <span class="<?= $penuh ? 'status-penuh' : 'status-tersedia'; ?>">
                                    <?= $penuh ? 'Penuh' : 'Tersedia'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="../controller/c_area_parkir.php?aksi=edit&id=<?= $row->id_area; ?>" class="btn-edit">
                                    Edit
                                </a>
                                <a
                                    href="../controller/c_area_parkir.php?aksi=hapus&id=<?= $row->id_area; ?>"
                                    class="btn-hapus"
                                    onclick="return confirm('Yakin ingin menghapus area ini?');"
                                >
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada data area parkir.</td>
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
