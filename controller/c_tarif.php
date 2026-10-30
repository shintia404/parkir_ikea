<?php
// Memulai sesi untuk menyimpan notifikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengambil file model tarif
include_once __DIR__ . '/../models/m_tarif.php';

// Inisialisasi koneksi database
$koneksi_obj = new koneksi();
$dbConnection = $koneksi_obj->koneksi;

// Menyiapkan model tarif
$tarif_model = new M_Tarif($dbConnection);

// Menangkap perintah aksi dari URL (tampil, edit, atau hapus)
$aksi = $_GET['aksi'] ?? 'tampil';
$id   = $_GET['id'] ?? null;

// Jenis kendaraan yang boleh dikelola di halaman tarif ini.
// Selain dua ini (mis. "lainnya") tidak boleh ditambah maupun diedit.
$jenis_diizinkan = ['mobil', 'motor'];


/* ==========================================================================
   1. LOGIKA TAMPIL DATA (READ)
   ========================================================================== */
if ($aksi === 'tampil') {
    // Hanya tampilkan tarif Motor & Mobil
    $data_tarif = $tarif_model->tampil_motor_mobil();
    include __DIR__ . '/../view/v_tarif_parkir.php';
    exit();
}


/* ==========================================================================
   2. LOGIKA EDIT (FORM EDIT TARIF)
   ========================================================================== */
elseif ($aksi === 'edit' && $id) {
    $data_edit = $tarif_model->tampil_data_by_id($id);

    // Pengaman: kalau data tidak ditemukan atau jenisnya bukan Motor/Mobil,
    // tidak boleh dibuka form editnya.
    if (!$data_edit || !in_array(strtolower($data_edit->jenis_kendaraan), $jenis_diizinkan)) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Tidak Bisa Diedit!',
            'teks'  => 'Hanya tarif Mobil dan Motor yang bisa diedit di halaman ini.'
        ];
        header("Location: ../controller/c_tarif.php?aksi=tampil");
        exit();
    }

    $data_tarif = $tarif_model->tampil_motor_mobil();
    include __DIR__ . '/../view/v_tarif_parkir.php';
    exit();
}


/* ==========================================================================
   3. LOGIKA HAPUS DATA (DELETE)
   ========================================================================== */
elseif ($aksi === 'hapus' && $id) {
    $result = $tarif_model->hapus_data($id);

    if ($result) {
        $_SESSION['pesan'] = [
            'tipe'  => 'success',
            'judul' => 'Berhasil Dihapus!',
            'teks'  => 'Data tarif berhasil dihapus.'
        ];
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal Hapus!',
            'teks'  => 'Data tarif gagal dihapus.'
        ];
    }

    header("Location: ../controller/c_tarif.php?aksi=tampil");
    exit();
}


/* ==========================================================================
   4. LOGIKA SIMPAN DATA (POST: TAMBAH & UPDATE)
   ========================================================================== */
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tarif        = $_POST['id_tarif'] ?? null;
    $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
    $tarif_per_jam   = (int) ($_POST['tarif_per_jam'] ?? 0);

    // Pengaman utama: apapun aksinya (tambah/update), jenis kendaraan
    // WAJIB Mobil atau Motor. Selain itu ditolak, tidak disimpan.
    if (!in_array(strtolower($jenis_kendaraan), $jenis_diizinkan)) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Jenis Tidak Diizinkan!',
            'teks'  => 'Tarif hanya boleh ditambahkan/diedit untuk kendaraan Mobil atau Motor.'
        ];
        header("Location: ../controller/c_tarif.php?aksi=tampil");
        exit();
    }

    // --- SUB-LOGIKA A: TAMBAH TARIF BARU ---
    if ($aksi === 'tambah') {
        $result = $tarif_model->tambah_data($jenis_kendaraan, $tarif_per_jam);

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Ditambahkan!',
                'teks'  => 'Data tarif baru berhasil disimpan.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Ditambahkan!',
                'teks'  => 'Gagal menyimpan data tarif baru.'
            ];
        }
        header("Location: ../controller/c_tarif.php?aksi=tampil");
        exit();
    }

    // --- SUB-LOGIKA B: UPDATE / UBAH TARIF ---
    elseif ($aksi === 'update') {
        $result = $tarif_model->ubah_data($jenis_kendaraan, $tarif_per_jam, $id_tarif);

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Diperbarui!',
                'teks'  => 'Data tarif berhasil diperbarui.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Diperbarui!',
                'teks'  => 'Gagal mengupdate data tarif.'
            ];
        }
        header("Location: ../controller/c_tarif.php?aksi=tampil");
        exit();
    }
}


/* ==========================================================================
   5. DEFAULT FALLBACK
   ========================================================================== */
else {
    header("Location: ../controller/c_tarif.php?aksi=tampil");
    exit();
}
