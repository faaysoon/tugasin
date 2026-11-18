CREATE DATABASE IF NOT EXISTS db_tugasin CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_tugasin;

DROP TABLE IF EXISTS pengumpulan_tugas;
DROP TABLE IF EXISTS mata_pelajaran;
DROP TABLE IF EXISTS siswa;

CREATE TABLE siswa (
  id_siswa INT AUTO_INCREMENT PRIMARY KEY,
  nama_siswa VARCHAR(100) NOT NULL,
  kelas VARCHAR(20) NOT NULL,
  jurusan VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE mata_pelajaran (
  id_mapel INT AUTO_INCREMENT PRIMARY KEY,
  nama_mapel VARCHAR(100) NOT NULL,
  nama_guru VARCHAR(100) NOT NULL,
  kelas VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pengumpulan_tugas (
  id_pengumpulan INT AUTO_INCREMENT PRIMARY KEY,
  id_siswa INT NOT NULL,
  id_mapel INT NOT NULL,
  nama_tugas VARCHAR(100) NOT NULL,
  tanggal_pengumpulan DATE NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Belum Dikumpulkan',
  CONSTRAINT fk_tugas_siswa FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tugas_mapel FOREIGN KEY (id_mapel) REFERENCES mata_pelajaran(id_mapel) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO siswa (nama_siswa, kelas, jurusan, email) VALUES
('Aisyah Putri', 'XI', 'IPA', 'aisyah@sekolah.id'),
('Budi Santoso', 'XI', 'IPS', 'budi@sekolah.id'),
('Citra Lestari', 'X', 'Bahasa', 'citra@sekolah.id'),
('Dimas Pratama', 'XII', 'IPA', 'dimas@sekolah.id'),
('Eka Rahmawati', 'X', 'IPS', 'eka@sekolah.id');

INSERT INTO mata_pelajaran (nama_mapel, nama_guru, kelas) VALUES
('Bahasa Indonesia', 'Bu Ratna', 'X'),
('Matematika', 'Pak Hendra', 'XI'),
('Biologi', 'Bu Sari', 'XI'),
('Sejarah', 'Pak Joko', 'XII');

INSERT INTO pengumpulan_tugas (id_siswa, id_mapel, nama_tugas, tanggal_pengumpulan, status) VALUES
(1, 2, 'Latihan Soal Trigonometri', '2026-11-10', 'Sudah Dikumpulkan'),
(1, 3, 'Laporan Praktikum Sel', '2026-11-12', 'Sudah Dikumpulkan'),
(2, 2, 'Latihan Soal Trigonometri', '2026-11-14', 'Belum Dikumpulkan'),
(3, 1, 'Menulis Teks Eksposisi', '2026-11-09', 'Sudah Dikumpulkan'),
(4, 4, 'Esai Proklamasi Kemerdekaan', '2026-11-15', 'Belum Dikumpulkan'),
(5, 1, 'Menulis Teks Eksposisi', '2026-11-16', 'Belum Dikumpulkan'),
(2, 3, 'Laporan Praktikum Sel', '2026-11-13', 'Sudah Dikumpulkan');
