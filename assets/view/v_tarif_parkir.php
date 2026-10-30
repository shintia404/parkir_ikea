<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan data tersedia dari controller
if (!isset($data_tarif)) {
    header('Location: ../controller/c_tarif.php?aksi=tampil');
    exit();
}

$mode_edit = isset($data_edit) && $data_edit;

// Ambil pesan notifikasi
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tarif Parkir - Parkir IKEA</title>

    <link rel="stylesheet" href="../assets/tarif.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- ================= HEADER ================= -->

    <div class="header">

        <div>
            <h1>💰 Tarif Parkir</h1>
            <p>Kelola data tarif parkir kendaraan IKEA</p>
        </div>

        <a href="DashBoard.php" class="btn-kembali">
            ← Kembali
        </a>

    </div>


    <!-- ================= NOTIFIKASI ================= -->

    <?php if ($pesan): ?>

        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error'; ?>">

            <strong>
                <?= htmlspecialchars($pesan['judul']); ?>
            </strong>

            <span>
                <?= htmlspecialchars($pesan['teks']); ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- ================= FORM TAMBAH / EDIT ================= -->

    <div class="card mb-card">

        <div class="card-header">

            <h2>
                <?= $mode_edit ? 'Edit Tarif' : 'Tambah Tarif'; ?>
            </h2>

        </div>


        <form
            action="../controller/c_tarif.php?aksi=<?= $mode_edit ? 'update' : 'tambah'; ?>"
            method="POST"
            class="form-tarif"
        >

            <!-- ID TARIF -->
            <input
                type="hidden"
                name="id_tarif"
                value="<?= htmlspecialchars($data_edit->id_tarif ?? ''); ?>"
            >


            <div class="form-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 520px;">


                <!-- ================= JENIS KENDARAAN ================= -->

                <div class="form-group">

                    <label for="jenis_kendaraan">
                        Pilih Kendaraan
                    </label>

                    <!--
                        Sengaja hanya Mobil & Motor yang bisa dipilih.
                        Jenis lain (mis. "Lainnya") tidak boleh ditambah/diedit di sini.
                    -->
                    <select
                        name="jenis_kendaraan"
                        id="jenis_kendaraan"
                        required
                    >

                        <option
                            value=""
                            disabled
                            <?= !$mode_edit ? 'selected' : ''; ?>
                        >
                            Pilih Jenis
                        </option>

                        <option
                            value="Mobil"
                            <?= ($mode_edit && strcasecmp($data_edit->jenis_kendaraan, 'Mobil') === 0) ? 'selected' : ''; ?>
                        >
                            Mobil
                        </option>

                        <option
                            value="Motor"
                            <?= ($mode_edit && strcasecmp($data_edit->jenis_kendaraan, 'Motor') === 0) ? 'selected' : ''; ?>
                        >
                            Motor
                        </option>

                    </select>

                </div>


                <!-- ================= TARIF PER JAM ================= -->

                <div class="form-group">

                    <label for="tarif_per_jam">
                        Tarif per Jam (Rp)
                    </label>

                    <input
                        type="number"
                        name="tarif_per_jam"
                        id="tarif_per_jam"
                        placeholder="2000"
                        value="<?= htmlspecialchars($data_edit->tarif_per_jam ?? ''); ?>"
                        min="0"
                        step="500"
                        required
                    >

                </div>

            </div>


            <!-- ================= TOMBOL ================= -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-tambah"
                >
                    <?= $mode_edit ? 'Update' : 'Tambah'; ?>
                </button>


                <?php if ($mode_edit): ?>

                    <a
                        href="../controller/c_tarif.php?aksi=tampil"
                        class="btn-batal"
                    >
                        Batal
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- ================= DATA TARIF ================= -->

    <div class="card">

        <div class="card-header">

            <h2>
                Data Tarif
            </h2>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif per Jam</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (!empty($data_tarif)): ?>

                    <?php
                    $no = 1;

                    foreach ($data_tarif as $row):
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <!-- JENIS KENDARAAN -->

                            <td>

                                <span class="<?= strcasecmp($row->jenis_kendaraan, 'Motor') === 0
                                    ? 'jenis-motor'
                                    : 'jenis-mobil'; ?>">

                                    <?= htmlspecialchars($row->jenis_kendaraan); ?>

                                </span>

                            </td>


                            <!-- TARIF -->

                            <td>
                                Rp
                                <?= number_format($row->tarif_per_jam, 0, ',', '.'); ?>
                            </td>


                            <!-- AKSI -->

                            <td>

                                <a
                                    href="../controller/c_tarif.php?aksi=edit&id=<?= $row->id_tarif; ?>"
                                    class="btn-edit"
                                >
                                    Edit
                                </a>


                                <a
                                    href="../controller/c_tarif.php?aksi=hapus&id=<?= $row->id_tarif; ?>"
                                    class="btn-hapus"
                                    onclick="return confirm('Yakin ingin menghapus tarif ini?');"
                                >
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="4"
                            class="empty-state"
                        >
                            Belum ada tarif Motor atau Mobil.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ================= JAVASCRIPT ================= -->

<script src="../assets/navbar.js"></script>

</body>
</html>
