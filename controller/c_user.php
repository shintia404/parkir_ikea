<?php
// Memulai sesi untuk menyimpan notifikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengambil file koneksi dan model user
// (pakai __DIR__ supaya path tidak bergantung pada dari mana script diakses)
include_once __DIR__ . '/../models/koneksi.php';
include_once __DIR__ . '/../models/m_user.php';

// Inisialisasi koneksi database
$koneksi_obj = new koneksi();
$dbConnection = $koneksi_obj->koneksi;

// Menyiapkan model user
$user_model = new M_user($dbConnection);

// Menangkap perintah aksi dari URL (tampil, edit, atau hapus)
$aksi = $_GET['aksi'] ?? 'tampil';
$id   = $_GET['id'] ?? null;


/* ==========================================================================
   1. LOGIKA TAMPIL DATA (READ)
   ========================================================================== */
if ($aksi === 'tampil') {
    $data_user = $user_model->getAll();
    include __DIR__ . '/../view/v_user.php';
    exit();
}


/* ==========================================================================
   2. LOGIKA EDIT (FORM EDIT USER)
   ========================================================================== */
elseif ($aksi === 'edit' && $id) {
    $data_edit = $user_model->getById((int) $id);
    $data_user = $user_model->getAll();
    include __DIR__ . '/../view/v_user.php';
    exit();
}


/* ==========================================================================
   3. LOGIKA HAPUS DATA (DELETE)
   ========================================================================== */
elseif ($aksi === 'hapus' && $id) {
    $result = $user_model->delete((int) $id);

    if ($result) {
        $_SESSION['pesan'] = [
            'tipe'  => 'success',
            'judul' => 'Berhasil Dihapus!',
            'teks'  => 'Data user berhasil dihapus.'
        ];
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal Hapus!',
            'teks'  => 'Data user gagal dihapus.'
        ];
    }

    header("Location: ../controller/c_user.php?aksi=tampil");
    exit();
}


/* ==========================================================================
   4. LOGIKA SIMPAN DATA (POST: TAMBAH & UPDATE)
   ========================================================================== */
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $data = [
        'nama_lengkap' => trim($_POST['nama_lengkap'] ?? ''),
        'username'     => trim($_POST['username'] ?? ''),
        'password'     => $_POST['password'] ?? '',
        'role'         => $_POST['role'] ?? '',
        'status_aktif' => $_POST['status_aktif'] ?? 1,
    ];

    // --- SUB-LOGIKA A: TAMBAH USER BARU ---
    if ($aksi === 'tambah') {
        try {
            $result = $user_model->create($data);
        } catch (\mysqli_sql_exception $e) {
            $result = false;
        }

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Ditambahkan!',
                'teks'  => 'User baru berhasil disimpan.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Ditambahkan!',
                'teks'  => 'Gagal menyimpan user baru. Pastikan username belum dipakai.'
            ];
        }
        header("Location: ../controller/c_user.php?aksi=tampil");
        exit();
    }

    // --- SUB-LOGIKA B: UPDATE / UBAH USER ---
    elseif ($aksi === 'update' && $id_user) {
        try {
            $result = $user_model->update((int) $id_user, $data);
        } catch (\mysqli_sql_exception $e) {
            $result = false;
        }

        if ($result) {
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Diperbarui!',
                'teks'  => 'Data user berhasil diperbarui.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Diperbarui!',
                'teks'  => 'Gagal mengupdate data user. Pastikan username belum dipakai.'
            ];
        }
        header("Location: ../controller/c_user.php?aksi=tampil");
        exit();
    }
}


/* ==========================================================================
   5. DEFAULT FALLBACK
   ========================================================================== */
else {
    header("Location: ../controller/c_user.php?aksi=tampil");
    exit();
}
