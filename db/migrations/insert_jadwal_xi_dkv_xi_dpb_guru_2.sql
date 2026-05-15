START TRANSACTION;

INSERT INTO `jadwal_mata_pelajaran`
(`kelas_jadwal_id`, `nama_kelas`, `nama_mata_pelajaran`, `guru_pengampu`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`)
SELECT
  v.`kelas_jadwal_id`,
  k.`nama_kelas`,
  v.`mapel`,
  2,
  v.`hari`,
  v.`jam_mulai`,
  v.`jam_selesai`,
  v.`ruang`
FROM `kelas_jadwal` k
JOIN (
  SELECT 21 kelas_jadwal_id, 'Senin' hari, 'Sejarah' mapel, '07:00:00' jam_mulai, '08:20:00' jam_selesai, 'RB. 203' ruang
  UNION ALL SELECT 21, 'Senin', 'B. Inggris', '08:20:00', '09:55:00', 'RB. 203'
  UNION ALL SELECT 21, 'Senin', 'Pend. Agama', '09:55:00', '11:55:00', 'RB. 203'
  UNION ALL SELECT 21, 'Senin', 'Matematika', '12:25:00', '13:45:00', 'RB. 203'
  UNION ALL SELECT 21, 'Selasa', 'PJOR', '07:00:00', '08:20:00', 'RB. 203'
  UNION ALL SELECT 21, 'Selasa', 'Pendidikan Pancasila', '08:20:00', '09:55:00', 'RB. 203'
  UNION ALL SELECT 21, 'Selasa', 'KK.DKV Perencanaan Desain Kreatif', '09:55:00', '12:25:00', '9. RD.201/Lab. DKV'
  UNION ALL SELECT 21, 'Selasa', 'Prosedur Operasional Standar DKV', '13:45:00', '15:05:00', '9. RD.201/Lab. DKV'
  UNION ALL SELECT 21, 'Rabu', 'KK.DKV. Manajemen Proyek dan Kolaborasi Desain', '07:40:00', '09:55:00', '9. RD.201/Lab. DKV'
  UNION ALL SELECT 21, 'Rabu', 'KK.DKV. Produksi Desain Digital', '09:55:00', '12:25:00', '9. RD.201/Lab. DKV'
  UNION ALL SELECT 21, 'Rabu', 'B. Jawa', '13:45:00', '15:05:00', 'RB. 203'
  UNION ALL SELECT 21, 'Kamis', 'MAPIL DKV (Program Siaran TV)', '07:40:00', '09:55:00', '9. RD.201/Lab. DKV'
  UNION ALL SELECT 21, 'Kamis', 'B. Inggris', '09:55:00', '11:55:00', 'RB. 203'
  UNION ALL SELECT 21, 'Kamis', 'Kreativitas, Inovasi, dan Kewirausahaan DKV', '12:25:00', '15:05:00', '10. RC.201/Lab. KJ'
  UNION ALL SELECT 21, 'Jumat', 'Karya dan Portofolio Desain', '07:40:00', '09:55:00', '7. RD.202/Lab. PF'
  UNION ALL SELECT 21, 'Jumat', 'B. Indo', '09:55:00', '11:55:00', 'RB. 203'

  UNION ALL SELECT 22, 'Senin', 'B. Inggris', '07:00:00', '08:20:00', 'RI. 304'
  UNION ALL SELECT 22, 'Senin', 'B. Indo', '08:20:00', '09:55:00', 'RI. 304'
  UNION ALL SELECT 22, 'Senin', 'B. Jawa', '10:35:00', '11:55:00', 'RI. 304'
  UNION ALL SELECT 22, 'Senin', 'Matematika', '12:25:00', '13:45:00', 'RI. 304'
  UNION ALL SELECT 22, 'Selasa', 'Sejarah', '07:00:00', '08:20:00', 'RI. 304'
  UNION ALL SELECT 22, 'Selasa', 'B. Inggris', '08:20:00', '09:55:00', 'RI. 304'
  UNION ALL SELECT 22, 'Selasa', 'Desain Busana', '09:55:00', '11:55:00', 'RI. 304'
  UNION ALL SELECT 22, 'Selasa', 'Mapil DPB. Bisnis Digital', '13:05:00', '15:05:00', '3. RH. 202/Lab. E-Comerce'
  UNION ALL SELECT 22, 'Rabu', 'Produksi Busana', '07:40:00', '10:35:00', '11. RJ. 101/Lab. Jahit'
  UNION ALL SELECT 22, 'Rabu', 'Produksi Busana', '11:15:00', '13:45:00', '11. RJ. 101/Lab. Jahit'
  UNION ALL SELECT 22, 'Kamis', 'Kreativitas, Inovasi dan Kewirausahaan DPB', '07:40:00', '10:35:00', '12. RJ. 201/R. Potong'
  UNION ALL SELECT 22, 'Kamis', 'Eksperimen Tekstil dan Desain Hiasan', '11:15:00', '13:45:00', '12. RJ. 201/R. Potong'
  UNION ALL SELECT 22, 'Kamis', 'Pendidikan Pancasila', '13:45:00', '15:05:00', 'RI. 304'
  UNION ALL SELECT 22, 'Jumat', 'PJOR', '07:00:00', '08:20:00', 'RI. 304'
  UNION ALL SELECT 22, 'Jumat', 'Mapil DPB. Bisnis Digital', '08:20:00', '09:55:00', 'RI. 304'
  UNION ALL SELECT 22, 'Jumat', 'Pend. Agama', '09:55:00', '11:55:00', 'RI. 304'
) v ON k.`id` = v.`kelas_jadwal_id`
WHERE NOT EXISTS (
  SELECT 1
  FROM `jadwal_mata_pelajaran` j
  WHERE j.`kelas_jadwal_id` = v.`kelas_jadwal_id`
    AND j.`nama_mata_pelajaran` = v.`mapel`
    AND j.`hari` = v.`hari`
    AND j.`jam_mulai` = v.`jam_mulai`
    AND j.`jam_selesai` = v.`jam_selesai`
    AND (j.`ruang` <=> v.`ruang`)
);

COMMIT;
