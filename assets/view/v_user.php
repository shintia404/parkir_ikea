<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// View ini adalah "template" murni: data ($data_user, dan $data_edit saat
// mode edit) disiapkan oleh controller (c_user.php) sebelum file ini
// di-include. Kalau halaman ini dibuka langsung tanpa lewat controller,
// arahkan ke controller supaya datanya selalu ada.
if (!isset($data_user)) {
    header('Location: ../controller/c_user.php?aksi=tampil');
    exit();
}

$mode_edit = isset($data_edit) && $data_edit;

// Ambil & bersihkan pesan notifikasi (sukses/gagal) dari session, kalau ada
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Data User</title>

    <link rel="stylesheet" href="../assets/user.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div>
            <h1>User</h1>
            <p>Kelola data pengguna parkir IKEA</p>
        </div>

        <a href="../view/DashBoard.php" class="btn-kembali">
            ← Kembali
        </a>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error'; ?>">
            <strong><?= htmlspecialchars($pesan['judul']); ?></strong>
            <span><?= htmlspecialchars($pesan['teks']); ?></span>
        </div>
    <?php endif; ?>


    <!-- FORM TAMBAH / EDIT USER -->
    <div class="card form-card">

        <h2><?= $mode_edit ? 'Edit User' : 'Tambah User'; ?></h2>

        <form action="../controller/c_user.php?aksi=<?= $mode_edit ? 'update' : 'tambah'; ?>" method="POST">

            <input type="hidden" name="id_user" value="<?= $data_edit->id_user ?? ''; ?>">

            <div class="form-row">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input
                        type="text"
                        name="nama_lengkap"
                        placeholder="Nama pengguna"
                        value="<?= htmlspecialchars($data_edit->nama_lengkap ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Masukkan username"
                        value="<?= htmlspecialchars($data_edit->username ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="<?= $mode_edit ? 'Kosongkan jika tidak diubah' : 'Masukkan password'; ?>"
                        <?= $mode_edit ? '' : 'required'; ?>
                    >
                </div>

                <div class="form-group">
                    <label>Role</label>

                    <select name="role" required>
                        <option value="" disabled <?= !$mode_edit ? 'selected' : ''; ?>>Pilih Role</option>
                        <option value="admin" <?= ($mode_edit && $data_edit->role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="petugas" <?= ($mode_edit && $data_edit->role == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                        <option value="owner" <?= ($mode_edit && $data_edit->role == 'owner') ? 'selected' : ''; ?>>Owner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>

                    <select name="status_aktif" required>
                        <option value="1" <?= (!$mode_edit || (string) $data_edit->status_aktif === '1') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= ($mode_edit && (string) $data_edit->status_aktif === '0') ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-tambah">
                    <?= $mode_edit ? 'Update' : 'Tambah'; ?>
                </button>

                <?php if ($mode_edit): ?>
                    <a href="../controller/c_user.php?aksi=tampil" class="btn-batal">Batal</a>
                <?php endif; ?>
            </div>

        </form>

    </div>


    <!-- DATA USER -->
    <div class="card data-card">

        <h2>Data User</h2>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($data_user)): ?>
                        <?php $no = 1; foreach ($data_user as $user): ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <span class="badge">
                                <?= htmlspecialchars($user->username); ?>
                            </span>
                        </td>

                        <td>
                            ••••••••
                        </td>

                        <td>
                            <?= htmlspecialchars($user->nama_lengkap); ?>
                        </td>

                        <td>
                            <span class="role">
                                <?= htmlspecialchars(ucfirst($user->role)); ?>
                            </span>
                        </td>

                        <td>
                            <span class="<?= $user->status_aktif ? 'status-aktif' : 'status-nonaktif'; ?>">
                                <?= $user->status_aktif ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </td>

                        <td class="aksi">

                            <a
                                href="../controller/c_user.php?aksi=edit&id=<?= $user->id_user; ?>"
                                class="btn-edit"
                            >
                                Edit
                            </a>

                            <a
                                href="../controller/c_user.php?aksi=hapus&id=<?= $user->id_user; ?>"
                                class="btn-hapus"
                                onclick="return confirm('Yakin ingin menghapus user ini?')"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

                        <?php endforeach; ?>
                    <?php else: ?>

                    <tr>
                        <td colspan="7" class="data-kosong">
                            Belum ada data user
                        </td>
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
