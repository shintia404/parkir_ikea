<?php
session_start();

// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "parkir_ikea");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM tb_user ORDER BY id_user DESC");

if (!$query) {
    die("Query gagal: " . mysqli_error($conn));
}
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

    <div class="header">
        <div>
            <h1>Manajemen Data User</h1>
            <p>Kelola akun pengguna sistem ParkirKu</p>
        </div>

        <a href="../index.php" class="btn-kembali">
            ← Kembali
        </a>
    </div>


    <!-- Tambah User -->
    <div class="card">

        <h2>Tambah User Baru</h2>

        <form action="TambahUser.php" method="POST">

            <div class="form-row">

                <div class="form-group">
                    <label>Nama Lengkap</label>

                    <input type="text"
                           name="nama_lengkap"
                           placeholder="Masukkan nama..."
                           required>
                </div>


                <div class="form-group">
                    <label>Username</label>

                    <input type="text"
                           name="username"
                           placeholder="Username"
                           required>
                </div>


                <div class="form-group">
                    <label>Password</label>

                    <input type="password"
                           name="password"
                           placeholder="Password"
                           required>
                </div>


                <div class="form-group">
                    <label>Role</label>

                    <select name="role">

                        <option value="Admin">Admin</option>
                        <option value="Petugas">Petugas</option>
                        <option value="Owner">Owner</option>

                    </select>
                </div>


                <div class="form-group">
                    <label>Status</label>

                    <select name="status">

                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>

                    </select>
                </div>

            </div>


            <button type="submit" class="btn-simpan">
                + Simpan User
            </button>

        </form>

    </div>


    <!-- Daftar User -->
    <div class="card">

        <div class="judul-daftar">

            <h2>Daftar User</h2>

            <span>
                Total: <?= mysqli_num_rows($query); ?>
            </span>

        </div>


        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>NAMA LENGKAP</th>
                    <th>USERNAME</th>
                    <th>ROLE</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>

            </thead>


            <tbody>

            <?php while ($row = mysqli_fetch_assoc($query)) { ?>

                <tr>

                    <td>
                        #<?= $row['id_user']; ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($row['nama_lengkap']); ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($row['username']); ?>
                    </td>


                    <td>
                        <span class="role">
                            <?= htmlspecialchars($row['role']); ?>
                        </span>
                    </td>


                    <td>
                        <span class="status_aktif">
                            <?= htmlspecialchars($row['status_aktif']); ?>
                        </span>
                    </td>


                    <td>

                        <a href="EditUser.php?id=<?= $row['id_user']; ?>"
                           class="btn-edit">
                            Edit
                        </a>


                        <a href="HapusUser.php?id=<?= $row['id_user']; ?>"
                           class="btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus user ini?')">
                            Hapus
                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>