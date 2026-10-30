
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan data tersedia dari controller
if (!isset($data_kendaraan)) {
    header('Location: ../controller/c_kendaraan.php?aksi=tampil');
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

    <title>Kendaraan - ParkirKu</title>

    <link rel="stylesheet" href="../assets/kendaraan.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- ================= HEADER ================= -->

    <div class="header">

        <div>
            <h1>Kendaraan</h1>
            <p>Kelola data kendaraan parkir IKEA</p>
        </div>

        <a href="../view/DashBoard.php" class="btn-kembali">
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
                <?= $mode_edit ? 'Edit Kendaraan' : 'Tambah Kendaraan'; ?>
            </h2>

        </div>


        <form
            action="../controller/c_kendaraan.php?aksi=<?= $mode_edit ? 'update' : 'tambah'; ?>"
            method="POST"
            class="form-kendaraan"
        >

            <!-- ID KENDARAAN -->
            <input
                type="hidden"
                name="id_kendaraan"
                value="<?= htmlspecialchars($data_edit->id_kendaraan ?? ''); ?>"
            >


            <div class="form-grid">


                <!-- ================= PLAT NOMOR ================= -->

                <div class="form-group">

                    <label for="plat_nomor">
                        Plat Nomor
                    </label>

                    <input
                        type="text"
                        name="plat_nomor"
                        id="plat_nomor"
                        placeholder="D 1234 ABC"
                        value="<?= htmlspecialchars($data_edit->plat_nomor ?? ''); ?>"
                        maxlength="11"
                        autocomplete="off"
                        required
                        oninput="formatPlat(this)"
                        title="Maksimal 2 huruf + 4 angka + 3 huruf"
                    >

                    <small class="plat-info">
                        Contoh: D 1234 ABC
                    </small>

                </div>


                <!-- ================= JENIS ================= -->

                <div class="form-group">

                    <label for="jenis_kendaraan">
                        Jenis
                    </label>

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
                            <?= ($mode_edit && $data_edit->jenis_kendaraan == 'Mobil') ? 'selected' : ''; ?>
                        >
                            Mobil
                        </option>

                        <option
                            value="Motor"
                            <?= ($mode_edit && $data_edit->jenis_kendaraan == 'Motor') ? 'selected' : ''; ?>
                        >
                            Motor
                        </option>

                    </select>

                </div>


                <!-- ================= WARNA ================= -->

                <div class="form-group">

                    <label for="warna">
                        Warna
                    </label>

                    <input
                        type="text"
                        name="warna"
                        id="warna"
                        placeholder="Hitam / Putih"
                        value="<?= htmlspecialchars($data_edit->warna ?? ''); ?>"
                        required
                    >

                </div>


                <!-- ================= PEMILIK ================= -->

                <div class="form-group">

                    <label for="pemilik">
                        Pemilik
                    </label>

                    <input
                        type="text"
                        name="pemilik"
                        id="pemilik"
                        placeholder="Nama pemilik"
                        value="<?= htmlspecialchars($data_edit->pemilik ?? ''); ?>"
                        required
                        oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '')"
                        title="Nama pemilik hanya boleh menggunakan huruf dan spasi"
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
                        href="../controller/c_kendaraan.php?aksi=tampil"
                        class="btn-batal"
                    >
                        Batal
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- ================= DATA KENDARAAN ================= -->

    <div class="card">

        <div class="card-header">

            <h2>
                Data Kendaraan
            </h2>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Warna</th>
                        <th>Pemilik</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (!empty($data_kendaraan)): ?>

                    <?php
                    $no = 1;

                    foreach ($data_kendaraan as $row):
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <!-- PLAT NOMOR -->

                            <td>

                                <span class="plat">

                                    <?= htmlspecialchars($row->plat_nomor); ?>

                                </span>

                            </td>


                            <!-- JENIS -->

                            <td>

                                <span class="<?= $row->jenis_kendaraan === 'Motor'
                                    ? 'jenis-motor'
                                    : 'jenis-mobil'; ?>">

                                    <?= htmlspecialchars($row->jenis_kendaraan); ?>

                                </span>

                            </td>


                            <!-- WARNA -->

                            <td>
                                <?= htmlspecialchars($row->warna); ?>
                            </td>


                            <!-- PEMILIK -->

                            <td>
                                <?= htmlspecialchars($row->pemilik); ?>
                            </td>


                            <!-- AKSI -->

                            <td>

                                <a
                                    href="../controller/c_kendaraan.php?aksi=edit&id=<?= $row->id_kendaraan; ?>"
                                    class="btn-edit"
                                >
                                    Edit
                                </a>


                                <a
                                    href="../controller/c_kendaraan.php?aksi=hapus&id=<?= $row->id_kendaraan; ?>"
                                    class="btn-hapus"
                                    onclick="return confirm('Yakin hapus data kendaraan ini?')"
                                >
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="6"
                            class="empty-state"
                        >
                            Belum ada data kendaraan.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ================= JAVASCRIPT ================= -->

<script>

function formatPlat(input) {

    // Ubah menjadi huruf kapital
    let value = input.value.toUpperCase();

    // Hanya izinkan huruf dan angka
    value = value.replace(/[^A-Z0-9]/g, '');


    // ==========================
    // BAGIAN 1
    // KODE WILAYAH
    // MAKSIMAL 2 HURUF
    // ==========================

    let bagian1 = '';

    while (
        value.length > 0 &&
        /^[A-Z]$/.test(value.charAt(0)) &&
        bagian1.length < 2
    ) {

        bagian1 += value.charAt(0);

        value = value.substring(1);
    }


    // ==========================
    // BAGIAN 2
    // ANGKA
    // MAKSIMAL 4 DIGIT
    // ==========================

    let bagian2 = '';

    while (
        value.length > 0 &&
        /^[0-9]$/.test(value.charAt(0)) &&
        bagian2.length < 4
    ) {

        bagian2 += value.charAt(0);

        value = value.substring(1);
    }


    // ==========================
    // BAGIAN 3
    // HURUF BELAKANG
    // MAKSIMAL 3 HURUF
    // ==========================

    let bagian3 = '';

    while (
        value.length > 0 &&
        /^[A-Z]$/.test(value.charAt(0)) &&
        bagian3.length < 3
    ) {

        bagian3 += value.charAt(0);

        value = value.substring(1);
    }


    // ==========================
    // GABUNGKAN
    // ==========================

    let hasil = '';

    if (bagian1 !== '') {
        hasil += bagian1;
    }

    if (bagian2 !== '') {
        hasil += ' ' + bagian2;
    }

    if (bagian3 !== '') {
        hasil += ' ' + bagian3;
    }


    // Masukkan kembali ke input
    input.value = hasil;
}


// ================================
// FORMAT DATA LAMA SAAT EDIT
// ================================

document.addEventListener('DOMContentLoaded', function () {

    const plat = document.getElementById('plat_nomor');

    if (plat && plat.value !== '') {
        formatPlat(plat);
    }

});

</script>


<script src="../assets/navbar.js"></script>

</body>
</html>
