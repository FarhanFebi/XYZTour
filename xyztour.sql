CREATE DATABASE IF NOT EXISTS xyztour;
USE xyztour;

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_event VARCHAR(100) NOT NULL,
  kuota INT NOT NULL,
  deskripsi TEXT
);

CREATE TABLE peserta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  email VARCHAR(100),
  no_wa VARCHAR(20),
  event_id INT,
  status_pembayaran ENUM('pending','valid','invalid') DEFAULT 'pending',
  bukti_bayar VARCHAR(255),
  waktu_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id)
);

INSERT INTO events (nama_event, kuota, deskripsi) VALUES
('Bandung', 50, 'Wisata ke Bandung dengan destinasi wisata alam dan kuliner'),
('Yogyakarta', 50, 'Wisata ke Yogyakarta, budaya dan sejarah');
