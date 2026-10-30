<?php
// File koneksi WAJIB di-include di sini, karena c_tarif.php hanya
// meng-include model ini lalu langsung memakai class "koneksi".
include_once __DIR__ . '/koneksi.php';

class M_Tarif {
    private $db;

    // Inisialisasi koneksi database
    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    // Fungsi SQL: Mengambil semua data tarif
    public function tampil_semua() {
        $result = [];
        $sql = "SELECT * FROM tb_tarif ORDER BY id_tarif ASC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Fungsi SQL: Hanya tarif Motor & Mobil (jenis lain tidak ditampilkan)
    public function tampil_motor_mobil() {
        $result = [];
        $sql = "SELECT * FROM tb_tarif
                WHERE jenis_kendaraan IN ('motor', 'mobil')
                ORDER BY jenis_kendaraan ASC, id_tarif ASC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Fungsi SQL: Mencari satu tarif berdasarkan ID (untuk proses edit)
    public function tampil_data_by_id($id_tarif) {
        $sql = "SELECT * FROM tb_tarif WHERE id_tarif = ?";
        $stmt = $this->db->prepare($sql);
        $id_tarif = (int) $id_tarif;
        $stmt->bind_param("i", $id_tarif);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    // Fungsi SQL: Menambahkan tarif baru
    public function tambah_data($jenis_kendaraan, $tarif_per_jam) {
        $sql = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $jenis_kendaraan = strtolower($jenis_kendaraan); // enum di DB: 'motor','mobil','lainnya'
        $tarif_per_jam   = (int) $tarif_per_jam;
        $stmt->bind_param("si", $jenis_kendaraan, $tarif_per_jam);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Mengubah tarif yang sudah tersimpan
    public function ubah_data($jenis_kendaraan, $tarif_per_jam, $id_tarif) {
        $sql = "UPDATE tb_tarif SET jenis_kendaraan = ?, tarif_per_jam = ? WHERE id_tarif = ?";
        $stmt = $this->db->prepare($sql);
        $jenis_kendaraan = strtolower($jenis_kendaraan);
        $tarif_per_jam   = (int) $tarif_per_jam;
        $id_tarif        = (int) $id_tarif;
        $stmt->bind_param("sii", $jenis_kendaraan, $tarif_per_jam, $id_tarif);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Fungsi SQL: Menghapus satu tarif berdasarkan ID-nya
    public function hapus_data($id_tarif) {
        $sql = "DELETE FROM tb_tarif WHERE id_tarif = ?";
        $stmt = $this->db->prepare($sql);
        $id_tarif = (int) $id_tarif;
        $stmt->bind_param("i", $id_tarif);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}
