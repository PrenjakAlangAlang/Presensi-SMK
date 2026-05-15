START TRANSACTION;

UPDATE `kelas_jadwal`
SET `nama_kelas` = CASE `id`
  WHEN 12 THEN 'XI BD'
  WHEN 15 THEN 'XI MP 1'
  WHEN 16 THEN 'XI MP 2'
  ELSE `nama_kelas`
END
WHERE `id` IN (12, 15, 16);

DELETE FROM `jadwal_mata_pelajaran`
WHERE `kelas_jadwal_id` IN (12, 15, 16);

INSERT INTO `jadwal_mata_pelajaran`
(`kelas_jadwal_id`, `nama_kelas`, `nama_mata_pelajaran`, `guru_pengampu`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`)
SELECT
  v.`kelas_jadwal_id`,
  k.`nama_kelas`,
  v.`mapel`,
  u.`id`,
  v.`hari`,
  v.`jam_mulai`,
  v.`jam_selesai`,
  v.`ruang`
FROM `kelas_jadwal` k
JOIN (
  SELECT 12 kelas_jadwal_id, 'Senin' hari, 'KK.BD. Perencanaan Bisnis' mapel, 'rifasausanariqah@smk7.sch.id' guru_email, '07:00:00' jam_mulai, '08:20:00' jam_selesai, 'RI. 101' ruang
  UNION ALL SELECT 12, 'Senin', 'KK.BD. Digital Marketing', 'amaliatulfirdaus@smk7.sch.id', '08:20:00', '11:15:00', '3. RH. 202/Lab. E-Comerce'
  UNION ALL SELECT 12, 'Senin', 'Pendidikan Pancasila', 'mutiarasabela@smk7.sch.id', '11:15:00', '13:05:00', 'RI. 101'
  UNION ALL SELECT 12, 'Senin', 'Sejarah', 'srisulastri@smk7.sch.id', '13:05:00', '14:25:00', 'RI. 101'
  UNION ALL SELECT 12, 'Selasa', 'KK.BD. Komunikasi Bisnis', 'rifasausanariqah@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 101'
  UNION ALL SELECT 12, 'Selasa', 'B. Inggris', 'dewinovitasari@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 101'
  UNION ALL SELECT 12, 'Selasa', 'Pend. Agama', 'rinaelistiana@smk7.sch.id', '09:55:00', '11:55:00', 'RI. 101'
  UNION ALL SELECT 12, 'Selasa', 'Pend. Agama Kristen', 'nurdianasasilimbong@smk7.sch.id', '09:55:00', '11:55:00', '16. RF.202/R. Agama Kristen'
  UNION ALL SELECT 12, 'Selasa', 'Pend. Agama Katolik', 'yuliatriutari@smk7.sch.id', '09:55:00', '11:55:00', '17. RB. 107/R. Agama Katolik'
  UNION ALL SELECT 12, 'Selasa', 'KK.BD. Marketing', 'rifasausanariqah@smk7.sch.id', '12:25:00', '14:25:00', 'RI. 101'
  UNION ALL SELECT 12, 'Rabu', 'KK.BD. Digital Branding', 'ditasarikusuma@smk7.sch.id', '07:00:00', '09:00:00', '3. RH. 202/Lab. E-Comerce'
  UNION ALL SELECT 12, 'Rabu', 'PJOR', 'jayaadipraptama@smk7.sch.id', '09:15:00', '10:35:00', 'RI. 101'
  UNION ALL SELECT 12, 'Rabu', 'Matematika', 'imayuniarti@smk7.sch.id', '10:35:00', '13:05:00', 'RI. 101'
  UNION ALL SELECT 12, 'Rabu', 'KK.BD. Digital Operation', 'ernawati@smk7.sch.id', '13:05:00', '14:25:00', '3. RH. 202/Lab. E-Comerce'
  UNION ALL SELECT 12, 'Kamis', 'B. Inggris', 'dewinovitasari@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 101'
  UNION ALL SELECT 12, 'Kamis', 'MAPIL BD. Administrasi Transaksi', 'amaliatulfirdaus@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 101'
  UNION ALL SELECT 12, 'Kamis', 'B. Indo', 'desikurniawati@smk7.sch.id', '09:55:00', '11:55:00', 'RI. 101'
  UNION ALL SELECT 12, 'Kamis', 'KK.BD. Digital onboarding', 'ditasarikusuma@smk7.sch.id', '12:25:00', '13:45:00', '3. RH. 202/Lab. E-Comerce'
  UNION ALL SELECT 12, 'Kamis', 'B. Jawa', 'yessiestifalia@smk7.sch.id', '13:45:00', '15:05:00', 'RI. 101'
  UNION ALL SELECT 12, 'Jumat', 'MAPIL BD. Administrasi Transaksi', 'amaliatulfirdaus@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 101'
  UNION ALL SELECT 12, 'Jumat', 'Kreativitas, Inovasi dan Kewirausahaan BD', 'ignatiusmurdonosigitsaputro@smk7.sch.id', '08:20:00', '11:55:00', 'RI. 101'

  UNION ALL SELECT 15, 'Senin', 'Matematika', 'erniyunita@smk7.sch.id', '07:00:00', '09:00:00', 'RI. 102'
  UNION ALL SELECT 15, 'Senin', 'MAPIL MP (Public speaking)', 'titikretnoningsih@smk7.sch.id', '09:15:00', '10:35:00', 'RI. 102'
  UNION ALL SELECT 15, 'Senin', 'KK.MP. Pengelolaan Rapat Pertemuan', 'ratnajunarti@smk7.sch.id', '10:35:00', '11:55:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 15, 'Senin', 'B. Indo', 'desikurniawati@smk7.sch.id', '12:25:00', '14:25:00', 'RI. 102'
  UNION ALL SELECT 15, 'Selasa', 'Pengelolaan Perjalanan Pimpinan', 'asihmarwati@smk7.sch.id', '07:00:00', '09:00:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 15, 'Selasa', 'PJOR', 'jayaadipraptama@smk7.sch.id', '09:15:00', '10:35:00', 'RI. 102'
  UNION ALL SELECT 15, 'Selasa', 'Kreativitas, Inovasi dan Kewirausahaan MP', 'nornaistritemawati@smk7.sch.id', '10:35:00', '11:55:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 15, 'Selasa', 'Pengelolaan Keuangan Sederhana', 'nurfitriana@smk7.sch.id', '12:25:00', '14:25:00', 'RI. 102'
  UNION ALL SELECT 15, 'Rabu', 'KK.MP. Pengelolaan Rapat/Pertemuan', 'ratnajunarti@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 102'
  UNION ALL SELECT 15, 'Rabu', 'Sejarah', 'srisulastri@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 102'
  UNION ALL SELECT 15, 'Rabu', 'Pendidikan Pancasila', 'harmini@smk7.sch.id', '09:55:00', '11:15:00', 'RI. 102'
  UNION ALL SELECT 15, 'Rabu', 'B. Inggris', 'kurniatiutami@smk7.sch.id', '11:15:00', '13:05:00', '19. Lab. Bahasa'
  UNION ALL SELECT 15, 'Rabu', 'Pengelolaan Perjalanan Pimpinan', 'asihmarwati@smk7.sch.id', '13:05:00', '14:25:00', 'RI. 102'
  UNION ALL SELECT 15, 'Kamis', 'Kreativitas, Inovasi dan Kewirausahaan MP', 'nornaistritemawati@smk7.sch.id', '07:00:00', '09:00:00', 'RI. 102'
  UNION ALL SELECT 15, 'Kamis', 'MAPIL MP (Public speaking)', 'titikretnoningsih@smk7.sch.id', '09:15:00', '10:35:00', 'RI. 102'
  UNION ALL SELECT 15, 'Kamis', 'Pengelolaan Keuangan Sederhana', 'nurfitriana@smk7.sch.id', '10:35:00', '13:05:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 15, 'Kamis', 'KK.MP. Komunikasi di tempat kerja', 'yeniwidiastuti@smk7.sch.id', '13:05:00', '15:05:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 15, 'Jumat', 'B. Inggris', 'kurniatiutami@smk7.sch.id', '07:00:00', '08:20:00', '19. Lab. Bahasa'
  UNION ALL SELECT 15, 'Jumat', 'B. Jawa', 'yessiestifalia@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 102'
  UNION ALL SELECT 15, 'Jumat', 'Pend. Agama', 'charisjauhari@smk7.sch.id', '09:55:00', '11:55:00', 'RI. 102'

  UNION ALL SELECT 16, 'Senin', 'Pengelolaan Keuangan Sederhana', 'nurfitriana@smk7.sch.id', '07:00:00', '09:00:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 16, 'Senin', 'Kreativitas, Inovasi dan Kewirausahaan MP', 'nornaistritemawati@smk7.sch.id', '09:15:00', '10:35:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 16, 'Senin', 'MAPIL MP (Public speaking)', 'titikretnoningsih@smk7.sch.id', '10:35:00', '11:55:00', 'RI. 103'
  UNION ALL SELECT 16, 'Senin', 'Pengelolaan Perjalanan Pimpinan', 'asihmarwati@smk7.sch.id', '12:25:00', '14:25:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 16, 'Selasa', 'B. Inggris', 'kurniatiutami@smk7.sch.id', '07:00:00', '08:20:00', '19. Lab. Bahasa'
  UNION ALL SELECT 16, 'Selasa', 'MAPIL MP (Public speaking)', 'titikretnoningsih@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 103'
  UNION ALL SELECT 16, 'Selasa', 'Pend. Agama', 'charisjauhari@smk7.sch.id', '09:55:00', '11:55:00', 'RI. 103'
  UNION ALL SELECT 16, 'Selasa', 'Matematika', 'imayuniarti@smk7.sch.id', '12:25:00', '14:25:00', 'RI. 103'
  UNION ALL SELECT 16, 'Rabu', 'Pengelolaan Perjalanan Pimpinan', 'asihmarwati@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 103'
  UNION ALL SELECT 16, 'Rabu', 'B. Inggris', 'kurniatiutami@smk7.sch.id', '08:20:00', '09:55:00', '19. Lab. Bahasa'
  UNION ALL SELECT 16, 'Rabu', 'KK.MP. Komunikasi di tempat kerja', 'yeniwidiastuti@smk7.sch.id', '09:55:00', '11:55:00', '2. RG.201/Lab. MP'
  UNION ALL SELECT 16, 'Rabu', 'Kreativitas, Inovasi dan Kewirausahaan MP', 'nornaistritemawati@smk7.sch.id', '12:25:00', '14:25:00', 'RI. 103'
  UNION ALL SELECT 16, 'Kamis', 'Pendidikan Pancasila', 'harmini@smk7.sch.id', '07:00:00', '08:20:00', 'RI. 103'
  UNION ALL SELECT 16, 'Kamis', 'B. Jawa', 'yessiestifalia@smk7.sch.id', '08:20:00', '09:55:00', 'RI. 103'
  UNION ALL SELECT 16, 'Kamis', 'Sejarah', 'srisulastri@smk7.sch.id', '09:55:00', '11:15:00', 'RI. 103'
  UNION ALL SELECT 16, 'Kamis', 'KK.MP. Pengelolaan Rapat Pertemuan', 'ratnajunarti@smk7.sch.id', '11:15:00', '13:05:00', 'RI. 103'
  UNION ALL SELECT 16, 'Kamis', 'Pengelolaan Keuangan Sederhana', 'nurfitriana@smk7.sch.id', '13:05:00', '15:05:00', 'RI. 103'
  UNION ALL SELECT 16, 'Jumat', 'B. Indo', 'desikurniawati@smk7.sch.id', '07:00:00', '09:00:00', 'RI. 103'
  UNION ALL SELECT 16, 'Jumat', 'PJOR', 'jayaadipraptama@smk7.sch.id', '09:15:00', '10:35:00', 'RI. 103'
  UNION ALL SELECT 16, 'Jumat', 'KK.MP. Pengelolaan Rapat Pertemuan', 'ratnajunarti@smk7.sch.id', '10:35:00', '11:55:00', '2. RG.201/Lab. MP'
) v ON k.`id` = v.`kelas_jadwal_id`
LEFT JOIN `users` u ON u.`email` = v.`guru_email` AND u.`role` = 'guru';

COMMIT;
