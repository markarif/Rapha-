-- ─────────────────────────────────────────────────────────────
-- Rapha Garden School — Database Setup
-- InfinityFree: create the database in cPanel first, then
-- click it in phpMyAdmin LEFT sidebar BEFORE importing this file
-- ─────────────────────────────────────────────────────────────

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(80)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- Gallery
CREATE TABLE IF NOT EXISTS gallery (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(255),
  image_path VARCHAR(255) NOT NULL,
  category   VARCHAR(50)  DEFAULT 'general',
  created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- News / Events / Notices
CREATE TABLE IF NOT EXISTS news (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(255) NOT NULL,
  category   ENUM('news','events','notices') DEFAULT 'news',
  excerpt    TEXT,
  content    LONGTEXT     NOT NULL,
  image      VARCHAR(255),
  created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     ON UPDATE CURRENT_TIMESTAMP
);

-- Fee structure
CREATE TABLE IF NOT EXISTS fees (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  grade_level     VARCHAR(50)  NOT NULL,
  term            VARCHAR(20)  NOT NULL,
  tuition_amount  INT          NOT NULL DEFAULT 0,
  levies_amount   INT                   DEFAULT 0,
  total_amount    INT          NOT NULL DEFAULT 0,
  year            YEAR         NOT NULL,
  notes           VARCHAR(255),
  created_at      DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- Contact form submissions
CREATE TABLE IF NOT EXISTS contacts (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  phone      VARCHAR(30),
  subject    VARCHAR(150),
  message    TEXT         NOT NULL,
  created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- Team members
CREATE TABLE IF NOT EXISTS team (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  role        VARCHAR(150) NOT NULL,
  description TEXT,
  icon        VARCHAR(10)  DEFAULT '👤',
  photo       VARCHAR(255),
  sort_order  INT          DEFAULT 0,
  created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- Downloadable forms
CREATE TABLE IF NOT EXISTS forms (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  filename    VARCHAR(255) NOT NULL,
  level       VARCHAR(100),
  created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- Admission applications
CREATE TABLE IF NOT EXISTS applications (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  student_name     VARCHAR(150) NOT NULL,
  date_of_birth    DATE,
  grade_applying   VARCHAR(50),
  parent_name      VARCHAR(150) NOT NULL,
  parent_phone     VARCHAR(30)  NOT NULL,
  parent_email     VARCHAR(150),
  address          VARCHAR(255),
  previous_school  VARCHAR(150),
  additional_info  TEXT,
  status           ENUM('new','reviewed','accepted','rejected') DEFAULT 'new',
  created_at       DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─── Default Admin User ───────────────────────────────────────
-- Username: admin  |  Password: RaphaAdmin2025
-- CHANGE THIS PASSWORD after first login!
-- To generate a new hash in PHP: echo password_hash('your-password', PASSWORD_DEFAULT);
INSERT INTO admin_users (username, password_hash) VALUES (
  'admin',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
) ON DUPLICATE KEY UPDATE id=id;

-- ─── Sample fee data ─────────────────────────────────────────
INSERT INTO fees (grade_level, term, tuition_amount, levies_amount, total_amount, year, notes) VALUES
('PP1',     'Term 1', 10000, 2000, 12000, 2025, 'Includes lunch'),
('PP1',     'Term 2', 10000, 2000, 12000, 2025, 'Includes lunch'),
('PP1',     'Term 3', 10000, 2000, 12000, 2025, 'Includes lunch'),
('PP2',     'Term 1', 10000, 2000, 12000, 2025, 'Includes lunch'),
('PP2',     'Term 2', 10000, 2000, 12000, 2025, 'Includes lunch'),
('PP2',     'Term 3', 10000, 2000, 12000, 2025, 'Includes lunch'),
('Grade 1', 'Term 1', 12000, 2000, 14000, 2025, 'Includes lunch & stationery'),
('Grade 1', 'Term 2', 12000, 2000, 14000, 2025, 'Includes lunch & stationery'),
('Grade 1', 'Term 3', 12000, 2000, 14000, 2025, 'Includes lunch & stationery'),
('Grade 4', 'Term 1', 14000, 2000, 16000, 2025, 'Includes lunch & stationery'),
('Grade 4', 'Term 2', 14000, 2000, 16000, 2025, 'Includes lunch & stationery'),
('Grade 4', 'Term 3', 14000, 2000, 16000, 2025, 'Includes lunch & stationery'),
('Grade 7', 'Term 1', 16000, 2000, 18000, 2025, 'JSS — includes ICT levy'),
('Grade 7', 'Term 2', 16000, 2000, 18000, 2025, 'JSS — includes ICT levy'),
('Grade 7', 'Term 3', 16000, 2000, 18000, 2025, 'JSS — includes ICT levy');
