<?php
include_once __DIR__ . '/koneksi.php';

class M_AreaParkir {
    private $db;

    // Inisialisasi koneksi database
    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    // Fungsi SQL: Mengambil semua data area parkir
    public function tampil_semua() {
        $result = [];
        $sql = "SELECT * FROM tb_area_parkir ORDER BY nama_area ASC, id_area ASC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Fungsi SQL: Mencari satu data area parkir berdasarkan ID (untuk proses edit)
    public function tampil_data_by_id($id_area) {
        $sql = "SELECT * FROM tb_area_parkir WHERE id_area = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_area);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    // Fungsi SQL: Menambahkan area parkir baru
    public function tambah_data($nama_area, $kapasitas, $terisi) {
        $sql = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $nama_area, $kapasitas, $terisi);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Mengubah data area parkir yang sudah tersimpan
    public function ubah_data($nama_area, $kapasitas, $terisi, $id_area) {
        $sql = "UPDATE tb_area_parkir SET nama_area = ?, kapasitas = ?, terisi = ? WHERE id_area = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("siii", $nama_area, $kapasitas, $terisi, $id_area);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Menghapus satu area parkir berdasarkan ID-nya
    public function hapus_data($id_area) {
        $sql = "DELETE FROM tb_area_parkir WHERE id_area = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_area);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Mengecek apakah area ini sudah pernah dipakai di transaksi parkir
    // (dipakai sebagai pengaman sebelum menghapus, supaya riwayat transaksi tidak jadi yatim)
    public function dipakai($id_area) {
        $sql = "SELECT COUNT(*) n FROM transaksi_parkir WHERE id_area = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_area);
        $stmt->execute();
        $result = $stmt->get_result();
        $n = (int) ($result->fetch_object()->n ?? 0);
        $stmt->close();
        return $n > 0;
    }
}
