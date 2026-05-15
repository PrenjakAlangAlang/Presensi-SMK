START TRANSACTION;

INSERT INTO `users` (`nama`, `email`, `password`, `role`)
SELECT
  v.`nama`,
  v.`email`,
  '$2y$10$6cT9rCw8UfrAHbQ89bPFHe4Gyiru4mV0buME81qpo0bwdWLmoxswy',
  'guru'
FROM (
  SELECT 'Charis Jauhari, S.Pd.' nama, 'charisjauhari@smk7.sch.id' email
  UNION ALL SELECT 'Widya Kusumawati, S.Pd.', 'widyakusumawati@smk7.sch.id'
  UNION ALL SELECT 'Rina Elistiana, S.Pd.', 'rinaelistiana@smk7.sch.id'
  UNION ALL SELECT 'Yulia Tri Utari, S.Pd.', 'yuliatriutari@smk7.sch.id'
  UNION ALL SELECT 'Nurdianasasi Limbong, S.Pd', 'nurdianasasilimbong@smk7.sch.id'
  UNION ALL SELECT 'Wahyu Wulandari, S.Pd. H', 'wahyuwulandari@smk7.sch.id'
  UNION ALL SELECT 'Harmini, S.Pd.', 'harmini@smk7.sch.id'
  UNION ALL SELECT 'Antonius Arlin Nurseta, S.S, M.Pd.', 'antoniusarlinnurseta@smk7.sch.id'
  UNION ALL SELECT 'Rini Yuni Astuti, S.Pd.', 'riniyuniastuti@smk7.sch.id'
  UNION ALL SELECT 'Desi Kurniawati, S.Pd', 'desikurniawati@smk7.sch.id'
  UNION ALL SELECT 'Muhammad Rifqi Aljabar, S.Pd', 'muhammadrifqialjabar@smk7.sch.id'
  UNION ALL SELECT 'Jaya Adi Praptama, S.Pd.', 'jayaadipraptama@smk7.sch.id'
  UNION ALL SELECT 'Sri Sulastri, S.Pd.', 'srisulastri@smk7.sch.id'
  UNION ALL SELECT 'Mutiara Sabela, S.Pd.', 'mutiarasabela@smk7.sch.id'
  UNION ALL SELECT 'Retno Setyomurti, S.Sn.', 'retnosetyomurti@smk7.sch.id'
  UNION ALL SELECT 'Ima Yuniarti, S.Pd.', 'imayuniarti@smk7.sch.id'
  UNION ALL SELECT 'Erni Yunita, S.Pd.', 'erniyunita@smk7.sch.id'
  UNION ALL SELECT 'Lina Widiyantari, S.Pd.', 'linawidiyantari@smk7.sch.id'
  UNION ALL SELECT 'Sri Puji Astuti, S.Pd.', 'sripujiastuti@smk7.sch.id'
  UNION ALL SELECT 'Rr. Diana Sukartiningsih, S.Pd.', 'rrdianasukartiningsih@smk7.sch.id'
  UNION ALL SELECT 'Dewi Novitasari, S.Pd.', 'dewinovitasari@smk7.sch.id'
  UNION ALL SELECT 'Kurniati Utami, S.Pd.', 'kurniatiutami@smk7.sch.id'
  UNION ALL SELECT 'Imelda Fani Swastika, S.Pd.', 'imeldafaniswastika@smk7.sch.id'
  UNION ALL SELECT 'Maryati, S.Pd.', 'maryati@smk7.sch.id'
  UNION ALL SELECT 'Aulia Windi Natriansyah', 'auliawindinatriansyah@smk7.sch.id'
  UNION ALL SELECT 'Sri Pardyanah, ST.M.Pd.', 'sripardyanah@smk7.sch.id'
  UNION ALL SELECT 'Lembah Srigati, S.Pd.', 'lembahsrigati@smk7.sch.id'
  UNION ALL SELECT 'Susilowati S.Pd.', 'susilowati@smk7.sch.id'
  UNION ALL SELECT 'Mustofa Saifulloh, S.Pd.', 'mustofasaifulloh@smk7.sch.id'
  UNION ALL SELECT 'Eko Harjito, S.Pd.', 'ekoharjito@smk7.sch.id'
  UNION ALL SELECT 'Indah Syanti Dewi, S.Pd.', 'indahsyantidewi@smk7.sch.id'
  UNION ALL SELECT 'Ernawati, S.Pd.', 'ernawati@smk7.sch.id'
  UNION ALL SELECT 'Amaliatul Firdaus. S.Pd.', 'amaliatulfirdaus@smk7.sch.id'
  UNION ALL SELECT 'Yeni Widiastuti, S.Pd.', 'yeniwidiastuti@smk7.sch.id'
  UNION ALL SELECT 'Dra. Suci Nugroho', 'sucinugroho@smk7.sch.id'
  UNION ALL SELECT 'Asih Marwati', 'asihmarwati@smk7.sch.id'
  UNION ALL SELECT 'Ratna Junarti, S.Pd.', 'ratnajunarti@smk7.sch.id'
  UNION ALL SELECT 'Norna Istri Temawati, S.Pd.', 'nornaistritemawati@smk7.sch.id'
  UNION ALL SELECT 'Nur Fitriana, S.Pd.', 'nurfitriana@smk7.sch.id'
  UNION ALL SELECT 'Titik Retno Ningsih, S.Pd.', 'titikretnoningsih@smk7.sch.id'
  UNION ALL SELECT 'Dita Sari Kusuma, S.Pd.', 'ditasarikusuma@smk7.sch.id'
  UNION ALL SELECT 'Rifa Sausan Ariqah, S.Pd.', 'rifasausanariqah@smk7.sch.id'
  UNION ALL SELECT 'Edi Sutriyono, S.E.', 'edisutriyono@smk7.sch.id'
  UNION ALL SELECT 'Darniati, S.St.Par.', 'darniati@smk7.sch.id'
  UNION ALL SELECT 'Sri Lestari, S.Pd., M.Pd.', 'srilestari@smk7.sch.id'
  UNION ALL SELECT 'Ana Hadi Prasetyawati, S.E.', 'anahadiprasetyawati@smk7.sch.id'
  UNION ALL SELECT 'Kingkin Kawuryan S.Pdt', 'kingkinkawuryan@smk7.sch.id'
  UNION ALL SELECT 'Rianitha Kusuma Dewi, S.ST. Par.', 'rianithakusumadewi@smk7.sch.id'
  UNION ALL SELECT 'Adik Kristien, S.Pd.', 'adikkristien@smk7.sch.id'
  UNION ALL SELECT 'Wuryadi Basuki, S.Pd.', 'wuryadibasuki@smk7.sch.id'
  UNION ALL SELECT 'Sahid Anwar, S.Tr.Anim.', 'sahidanwar@smk7.sch.id'
  UNION ALL SELECT 'Ardani Pramono, S.Pd.', 'ardanipramono@smk7.sch.id'
  UNION ALL SELECT 'Khasna Nur Fauziah, S.Pd.', 'khasnanurfauziah@smk7.sch.id'
  UNION ALL SELECT 'Yosef Endi Sonatha, S.Si.', 'yosefendisonatha@smk7.sch.id'
  UNION ALL SELECT 'Wulan Afriani, S.Pd', 'wulanafriani@smk7.sch.id'
  UNION ALL SELECT 'Kartika Dwi Hidayati, S.Pd', 'kartikadwihidayati@smk7.sch.id'
  UNION ALL SELECT 'Retno Murtiningrum, S.Pd.', 'retnomurtiningrum@smk7.sch.id'
  UNION ALL SELECT 'Tri Murdiati, S.Pd.', 'trimurdiati@smk7.sch.id'
  UNION ALL SELECT 'Drs. Aris Taryana', 'aristaryana@smk7.sch.id'
  UNION ALL SELECT 'Ignatius Murdono Sigit Saputro, S.Pd.', 'ignatiusmurdonosigitsaputro@smk7.sch.id'
  UNION ALL SELECT 'Yessi Estifalia, S.Pd.', 'yessiestifalia@smk7.sch.id'
  UNION ALL SELECT 'Asvi Dema Vieri, S.Pd.', 'asvidemavieri@smk7.sch.id'
  UNION ALL SELECT 'Rudan Gumanti, S.Pd.', 'rudangumanti@smk7.sch.id'
) v
WHERE NOT EXISTS (
  SELECT 1
  FROM `users` u
  WHERE u.`email` = v.`email`
     OR (u.`nama` = v.`nama` AND u.`role` = 'guru')
);

COMMIT;
