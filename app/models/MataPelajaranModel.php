<?php

require_once __DIR__ . '/JadwalMataPelajaranModel.php';

class MataPelajaranModel extends JadwalMataPelajaranModel {
    public function getAllMataPelajaran() {
        return $this->getAllJadwal();
    }

    public function getMataPelajaranById($id) {
        return $this->getJadwalById($id);
    }

    public function createMataPelajaran($data) {
        return $this->createJadwal($data);
    }

    public function updateMataPelajaran($data) {
        return $this->updateJadwal($data);
    }

    public function deleteMataPelajaran($id) {
        return $this->deleteJadwal($id);
    }

    public function getMataPelajaranByGuru($guru_id) {
        return $this->getJadwalByGuru($guru_id);
    }

    public function getSiswaInMataPelajaran($mata_pelajaran_id) {
        return $this->getSiswaInJadwal($mata_pelajaran_id);
    }

    public function getTotalSiswaByMataPelajaran($mata_pelajaran_id) {
        return $this->getTotalSiswaByJadwal($mata_pelajaran_id);
    }

    public function addSiswaToMataPelajaran($siswa_id, $mata_pelajaran_id) {
        return $this->addSiswaToJadwal($siswa_id, $mata_pelajaran_id);
    }

    public function removeSiswaFromMataPelajaran($siswa_id, $mata_pelajaran_id) {
        return $this->removeSiswaFromJadwal($siswa_id, $mata_pelajaran_id);
    }

    public function getMataPelajaranBySiswa($siswa_id) {
        return $this->getJadwalBySiswa($siswa_id);
    }

    public function getAllMataPelajaranWithKelas() {
        return $this->getAllJadwal();
    }
}
?>
