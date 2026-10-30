<?php

class M_user {
    private $db;
    private string $table = "tb_user"; 

    // Constructor otomatis mengambil koneksi jika tidak dikirim dari luar
    public function __construct($koneksi = null) {
        if ($koneksi !== null) {
            $this->db = $koneksi;
        } else {
            // Memanggil file koneksi milikmu
            require_once  'koneksi.php'; // Sesuaikan lokasi file koneksimu
            $dbObj = new koneksi();
            $this->db = $dbObj->hubungkan(); // Menggunakan fungsi hubungkan() dari kelas koneksi
        }
    }

    // Ambil Semua Data
    public function getAll(): array {
        $result = [];
        $sql = "SELECT * FROM {$this->table} ORDER BY id_user DESC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Ambil Data Berdasarkan ID
    public function getById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = $res->fetch_object();
        $stmt->close();
        return $data;
    }

    // Tambah Data
    public function create(array $data): bool {
        $sql = "INSERT INTO {$this->table} (nama_lengkap, username, password, role, status_aktif) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt->bind_param(
            "sssss", 
            $data['nama_lengkap'], 
            $data['username'], 
            $hashedPassword, 
            $data['role'], 
            $data['status_aktif']
        );

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Update Data
    public function update(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $sql = "UPDATE {$this->table} SET 
                    nama_lengkap = ?, 
                    username = ?, 
                    password = ?, 
                    role = ?, 
                    status_aktif = ? 
                    WHERE id_user = ?";
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->bind_param(
                "sssssi", 
                $data['nama_lengkap'], 
                $data['username'], 
                $hashedPassword, 
                $data['role'], 
                $data['status_aktif'], 
                $id
            );
        } else {
            $sql = "UPDATE {$this->table} SET 
                    nama_lengkap = ?, 
                    username = ?, 
                    role = ?, 
                    status_aktif = ? 
                    WHERE id_user = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                "ssssi", 
                $data['nama_lengkap'], 
                $data['username'], 
                $data['role'], 
                $data['status_aktif'], 
                $id
            );
        }

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Hapus Data
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}