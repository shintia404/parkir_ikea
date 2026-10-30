<?php
include_once __DIR__ . '/koneksi.php';

class M_Kendaraan {
    private $db;

    // Inisialisasi koneksi database
    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    // Fungsi SQL: Mengambil semua data kendaraan
    public function tampil_semua() {
        $result = [];
        $sql = "SELECT * FROM tb_kendaraan ORDER BY id_kendaraan DESC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Fungsi SQL: Mencari satu data kendaraan berdasarkan ID (untuk proses edit)
    public function tampil_data_by_id($id_kendaraan) {
        $sql = "SELECT * FROM tb_kendaraan WHERE id_kendaraan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_kendaraan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    // Fungsi SQL: Menambahkan data kendaraan baru
    public function tambah_data($plat_nomor, $jenis_kendaraan, $warna, $pemilik) {
        $sql = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $plat_nomor, $jenis_kendaraan, $warna, $pemilik);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Mengubah data kendaraan yang sudah tersimpan
    public function ubah_data($plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_kendaraan) {
        $sql = "UPDATE tb_kendaraan SET plat_nomor = ?, jenis_kendaraan = ?, warna = ?, pemilik = ? WHERE id_kendaraan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_kendaraan);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Menghapus satu kendaraan berdasarkan ID-nya
    public function hapus_data($id_kendaraan) {
        $sql = "DELETE FROM tb_kendaraan WHERE id_kendaraan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_kendaraan);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}