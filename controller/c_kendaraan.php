<?php
// Memulai sesi untuk menyimpan data login dan notifikasi SweetAlert
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengambil file koneksi dan model kendaraan
// (pakai __DIR__ supaya path tidak bergantung pada dari mana script diakses)
include_once __DIR__ . '/../models/m_kendaraan.php'; 

// Inisialisasi koneksi database
$koneksi_obj = new koneksi();
$dbConnection = $koneksi_obj->koneksi; 

// Menyiapkan model kendaraan untuk mengolah tabel kendaraan di database
$kendaraan_model = new m_Kendaraan($dbConnection);

// Menangkap perintah aksi dari URL (tampil, edit, atau hapus)
$aksi = $_GET['aksi'] ?? 'tampil'; 
$id   = $_GET['id'] ?? null;


/* ==========================================================================
   1. LOGIKA TAMPIL DATA (READ)
   ========================================================================== */
if ($aksi === 'tampil') {
    // Meminta semua daftar kendaraan dari model untuk ditampilkan ke user
    $data_kendaraan = $kendaraan_model->tampil_semua();
    include __DIR__ . '/../view/v_kendaraan.php';
    exit();
} 


/* ==========================================================================
   2. LOGIKA EDIT (FORM EDIT KENDARAAN)
   ========================================================================== */
elseif ($aksi === 'edit' && $id) {
    // Mengambil data satu kendaraan berdasarkan ID-nya untuk dimasukkan ke form edit
    // Catatan: file v_update_kendaraan.php terpisah tidak pernah ada di project ini,
    // jadi ini akan selalu error "failed to open stream". Solusinya: pakai ulang
    // v_kendaraan.php yang formnya memang sudah dirancang untuk mode tambah & edit
    // sekaligus (lihat pengecekan isset($data_edit) di form-nya).
    $data_edit      = $kendaraan_model->tampil_data_by_id($id);
    $data_kendaraan = $kendaraan_model->tampil_semua();
    include __DIR__ . '/../view/v_kendaraan.php';
    exit();
} 


/* ==========================================================================
   3. LOGIKA HAPUS DATA (DELETE)
   ========================================================================== */
elseif ($aksi === 'hapus' && $id) {
    // Meminta model untuk menghapus data di database
    $result = $kendaraan_model->hapus_data($id);
    
    if ($result) {
        $_SESSION['pesan'] = [
            'tipe'  => 'success',
            'judul' => 'Berhasil Dihapus!',
            'teks'  => 'Data kendaraan berhasil dihapus.'
        ];
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal Hapus!',
            'teks'  => 'Data kendaraan gagal dihapus.'
        ];
    }
    
    header("Location: ../controller/c_kendaraan.php?aksi=tampil");
    exit();
}


/* ==========================================================================
   4. LOGIKA SIMPAN DATA (POST: TAMBAH & UPDATE)
   ========================================================================== */
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menangkap inputan dari form kendaraan
    $id_kendaraan    = $_POST['id_kendaraan'] ?? null;
    $plat_nomor      = strtoupper(trim($_POST['plat_nomor'] ?? ''));
    $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
    $warna           = trim($_POST['warna'] ?? '');
    $pemilik         = trim($_POST['pemilik'] ?? '');

    // --- SUB-LOGIKA A: TAMBAH KENDARAAN BARU ---
    if ($aksi === 'tambah') {
        $result = $kendaraan_model->tambah_data($plat_nomor, $jenis_kendaraan, $warna, $pemilik);
        
        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Ditambahkan!',
                'teks'  => 'Data kendaraan baru berhasil disimpan.'
            ];
            header("Location: ../controller/c_kendaraan.php?aksi=tampil");
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Ditambahkan!',
                'teks'  => 'Gagal menyimpan data kendaraan baru.'
            ];
            // HTTP_REFERER tidak selalu ada (browser/proxy bisa menyembunyikannya),
            // jadi pakai fallback ke halaman tampil supaya tidak error.
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../controller/c_kendaraan.php?aksi=tampil'));
        }
        exit();
    } 

    // --- SUB-LOGIKA B: UPDATE / UBAH KENDARAAN ---
    elseif ($aksi === 'update') {
        $result = $kendaraan_model->ubah_data($plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_kendaraan);
        
        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Diperbarui!',
                'teks'  => 'Data kendaraan berhasil diperbarui.'
            ];
            header("Location: ../controller/c_kendaraan.php?aksi=tampil");
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Diperbarui!',
                'teks'  => 'Gagal mengupdate data kendaraan.'
            ];
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../controller/c_kendaraan.php?aksi=tampil'));
        }
        exit();
    }
}


/* ==========================================================================
   5. DEFAULT FALLBACK
   ========================================================================== */
else {
    header("Location: ../controller/c_kendaraan.php?aksi=tampil");
    exit();
}