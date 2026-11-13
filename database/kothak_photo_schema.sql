-- ============================================================================
-- KOTHAK PHOTO SMART QUEUE & BOOKING SYSTEM — MYSQL RELATIONAL SCHEMA
-- Environment : XAMPP / Laragon (MySQL 8.0+ / MariaDB 10.6+ InnoDB)
-- Referensi UI: https://kothak-cafe.frostyice.id/
-- ============================================================================

CREATE DATABASE IF NOT EXISTS kothak_photo_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kothak_photo_db;

-- 1. Tabel users (Pelanggan, Kasir, Admin)
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone_whatsapp VARCHAR(25) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer', 'cashier', 'admin') NOT NULL DEFAULT 'customer',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- 2. Tabel packages (Manajemen Paket Foto Studio)
CREATE TABLE packages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT NOT NULL,
  duration INT UNSIGNED NOT NULL COMMENT 'Durasi sesi foto dalam satuan menit',
  price DECIMAL(10, 2) NOT NULL COMMENT 'Harga paket dalam Rupiah (IDR)',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_packages_active (is_active)
) ENGINE=InnoDB;

-- 3. Tabel schedules (Jadwal & Kuota Slot Harian Studio)
CREATE TABLE schedules (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  capacity INT UNSIGNED NOT NULL DEFAULT 3 COMMENT 'Kuota maksimal reservasi pada slot waktu ini',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_schedule_slot (date, start_time, end_time),
  INDEX idx_schedule_date (date)
) ENGINE=InnoDB;

-- 4. Tabel bookings (Transaksi Reservasi & Nomor Antrean Pintar)
-- Menggunakan DATETIME(3) (presisi milidetik) untuk mencegah race-condition saat klik bersamaan
CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  package_id INT UNSIGNED NOT NULL,
  schedule_id INT UNSIGNED NOT NULL,
  booking_date DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  status ENUM(
    'waiting_verification',
    'confirmed',
    'checked_in',
    'in_studio',
    'completed',
    'cancelled'
  ) NOT NULL DEFAULT 'waiting_verification',
  queue_number VARCHAR(20) NOT NULL,
  studio_backdrop VARCHAR(80) DEFAULT 'Electric Blue Backdrop',
  whatsapp_sent_at DATETIME DEFAULT NULL,
  checked_in_at DATETIME DEFAULT NULL,
  UNIQUE KEY uq_date_queue_number (schedule_id, queue_number),
  INDEX idx_bookings_status (status),
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_bookings_package
    FOREIGN KEY (package_id) REFERENCES packages(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_bookings_schedule
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Tabel payments (Bukti Pembayaran QRIS Statis & Verifikasi Kasir)
CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL UNIQUE,
  proof_image_path VARCHAR(255) NOT NULL COMMENT 'Path file bukti transfer QRIS di public/uploads/payment_proofs/',
  payment_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
  verified_by INT UNSIGNED DEFAULT NULL COMMENT 'ID user kasir yang memverifikasi',
  verified_at DATETIME DEFAULT NULL,
  rejection_note VARCHAR(255) DEFAULT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  INDEX idx_payments_status (payment_status),
  CONSTRAINT fk_payments_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_payments_verifier
    FOREIGN KEY (verified_by) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Data Awal (Seeder)
INSERT INTO users (name, email, phone_whatsapp, password, role) VALUES
('Hana Naila Rahmadina', 'hananaeylaa@gmail.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Raka Pratama (Kasir)', 'cashier@kothakphoto.id', '08132674377', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier'),
('Admin Kothak Studio', 'admin@kothakphoto.id', '08132674377', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO packages (name, description, duration, price, is_active) VALUES
('Kothak Solo / Duo Express', 'Sesi photobox 10 menit, 2 strip cetak 2R, soft file HD via QR.', 10, 25000.00, 1),
('Kothak Signature Box (2-4 Orang)', 'Sesi photobox 15 menit, 2 cetak 4R, soft file HD + GIF.', 15, 35000.00, 1),
('Warm Minimalist Portrait Session', 'Sesi 15 menit di Background Warm Cream Minimalist + cetak 4R.', 15, 35000.00, 1),
('Bestie Squad Frame (4-6 Orang)', 'Sesi 20 menit untuk 4-6 orang, 4 cetak strip 4R + semua soft file.', 20, 50000.00, 1),
('Kopi & Frame Couple Bundle', 'Sesi Signature Photobox 15 menit + 2 Iced Signature Coffee Kothak.', 15, 65000.00, 1);

INSERT INTO schedules (date, start_time, end_time, capacity) VALUES
('2026-11-13', '14:00:00', '14:30:00', 3),
('2026-11-13', '15:00:00', '15:30:00', 3),
('2026-11-13', '16:00:00', '16:30:00', 3),
('2026-11-13', '17:00:00', '17:30:00', 3);
