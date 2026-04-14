-- ============================================
-- LabourLink Database Setup Queries
-- Run these queries in phpMyAdmin SQL tab
-- ============================================

-- 1. Add location columns to users table (if not exists)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS location_updated_at DATETIME DEFAULT NULL;

-- 2. Create jobs table
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    job_title VARCHAR(200) NOT NULL,
    work_type ENUM('planting','harvesting','irrigation','weeding','pesticide','ploughing','other') NOT NULL,
    crop_type VARCHAR(100) DEFAULT NULL,
    field_size DECIMAL(10, 2) DEFAULT NULL,
    field_size_unit ENUM('acres','hectares','bigha') DEFAULT 'acres',
    labours_required INT NOT NULL,
    wage_amount DECIMAL(10, 2) NOT NULL,
    wage_type ENUM('per_day','per_hour','fixed') DEFAULT 'per_day',
    work_duration INT DEFAULT NULL,
    duration_unit ENUM('days','hours','weeks') DEFAULT 'days',
    start_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    work_hours_start TIME DEFAULT NULL,
    work_hours_end TIME DEFAULT NULL,
    location_address TEXT NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    requirements TEXT DEFAULT NULL,
    facilities TEXT DEFAULT NULL,
    status ENUM('open','in_progress','completed','cancelled') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create job applications table
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    labour_id INT NOT NULL,
    status ENUM('pending','accepted','rejected','withdrawn') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (labour_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, labour_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Verification Queries (Optional - to check)
-- ============================================

-- Check if columns were added to users table
DESCRIBE users;

-- Check if jobs table was created
DESCRIBE jobs;

-- Check if job_applications table was created
DESCRIBE job_applications;

-- View all tables in database
SHOW TABLES;
