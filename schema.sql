-- นำเข้าผ่าน phpMyAdmin: เลือกฐานข้อมูล -> แท็บ Import -> เลือกไฟล์นี้ -> Go
CREATE TABLE IF NOT EXISTS `users` (
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL,
  `branch` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reports` (
  `branch` VARCHAR(50) NOT NULL,
  `report_type` VARCHAR(50) NOT NULL,
  `report_date` DATE NOT NULL,
  `inputs` MEDIUMTEXT,
  `grid` MEDIUMTEXT,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`branch`, `report_type`, `report_date`),
  KEY `idx_date` (`report_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
