<?php
// Memulai sesi untuk menyimpan notifikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengambil file model area parkir
include_once __DIR__ . '/../models/m_area_parkir.php';

// Inisialisasi koneksi database
$koneksi_obj = new koneksi();
$dbConnection = $koneksi_obj->koneksi;

// Menyiapkan model area parkir
$area_model = new M_AreaParkir($dbConnection);

// Menangkap perintah aksi dari URL (tampil, edit, atau hapus)
$aksi = $_GET['aksi'] ?? 'tampil';
$id   = $_GET['id'] ?? null;


/* ==========================================================================
   1. LOGIKA TAMPIL DATA (READ)
   ========================================================================== */
if ($aksi === 'tampil') {
    $data_area = $area_model->tampil_semua();
    include __DIR__ . '/../view/v_area_parkir.php';
    exit();
}


/* ==========================================================================
   2. LOGIKA EDIT (FORM EDIT AREA)
   ========================================================================== */
elseif ($aksi === 'edit' && $id) {
    $data_edit = $area_model->tampil_data_by_id($id);

    if (!$data_edit) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Tidak Ditemukan!',
            'teks'  => 'Data area parkir yang mau diedit tidak ditemukan.'
        ];
        header("Location: ../controller/c_area_parkir.php?aksi=tampil");
        exit();
    }

    $data_area = $area_model->tampil_semua();
    include __DIR__ . '/../view/v_area_parkir.php';
    exit();
}


/* ==========================================================================
   3. LOGIKA HAPUS DATA (DELETE)
   ========================================================================== */
elseif ($aksi === 'hapus' && $id) {
    // Pengaman: area yang sudah punya riwayat transaksi tidak boleh dihapus,
    // supaya data transaksi lama tidak jadi yatim (kehilangan area asalnya).
    if ($area_model->dipakai($id)) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Tidak Bisa Dihapus!',
            'teks'  => 'Area ini sudah punya riwayat transaksi parkir, jadi tidak bisa dihapus.'
        ];
        header("Location: ../controller/c_area_parkir.php?aksi=tampil");
        exit();
    }

    $result = $area_model->hapus_data($id);

    if ($result) {
        $_SESSION['pesan'] = [
            'tipe'  => 'success',
            'judul' => 'Berhasil Dihapus!',
            'teks'  => 'Data area parkir berhasil dihapus.'
        ];
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal Hapus!',
            'teks'  => 'Data area parkir gagal dihapus.'
        ];
    }

    header("Location: ../controller/c_area_parkir.php?aksi=tampil");
    exit();
}


/* ==========================================================================
   4. LOGIKA SIMPAN DATA (POST: TAMBAH & UPDATE)
   ========================================================================== */
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_area    = $_POST['id_area'] ?? null;
    $nama_area  = trim($_POST['nama_area'] ?? '');
    $kapasitas  = (int) ($_POST['kapasitas'] ?? 0);
    $terisi     = (int) ($_POST['terisi'] ?? 0);

    // --- VALIDASI DASAR ---
    if ($nama_area === '' || $kapasitas <= 0) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Data Tidak Lengkap!',
            'teks'  => 'Nama area wajib diisi dan kapasitas harus lebih dari 0.'
        ];
        header("Location: ../controller/c_area_parkir.php?aksi=tampil");
        exit();
    }

    // Jaga-jaga: jumlah terisi tidak boleh negatif atau melebihi kapasitas
    if ($terisi < 0) $terisi = 0;
    if ($terisi > $kapasitas) $terisi = $kapasitas;

    // --- SUB-LOGIKA A: TAMBAH AREA BARU ---
    if ($aksi === 'tambah') {
        $result = $area_model->tambah_data($nama_area, $kapasitas, $terisi);

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Ditambahkan!',
                'teks'  => 'Area parkir baru berhasil disimpan.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Ditambahkan!',
                'teks'  => 'Gagal menyimpan area parkir baru.'
            ];
        }
        header("Location: ../controller/c_area_parkir.php?aksi=tampil");
        exit();
    }

    // --- SUB-LOGIKA B: UPDATE / UBAH AREA ---
    elseif ($aksi === 'update' && $id_area) {
        $result = $area_model->ubah_data($nama_area, $kapasitas, $terisi, $id_area);

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Diperbarui!',
                'teks'  => 'Data area parkir berhasil diperbarui.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Diperbarui!',
                'teks'  => 'Gagal mengupdate data area parkir.'
            ];
        }
        header("Location: ../controller/c_area_parkir.php?aksi=tampil");
        exit();
    }
}


/* ==========================================================================
   5. DEFAULT FALLBACK
   ========================================================================== */
else {
    header("Location: ../controller/c_area_parkir.php?aksi=tampil");
    exit();
}
