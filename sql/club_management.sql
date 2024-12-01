-- Create the database
CREATE DATABASE club_management_system;

-- Use the database
USE club_management_system;

-- ----------------------------
-- Table for Users (Members)
-- ----------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(100) UNIQUE NOT NULL ,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    gender ENUM('Male', 'Female', 'Non-binary', 'Other', 'Prefer not to say') DEFAULT 'Prefer not to say',
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('member', 'club_admin', 'root_admin') DEFAULT 'member',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    phone_number VARCHAR(15),
    profile_picture VARCHAR(255) COMMENT 'Path to profile picture',
    current_year ENUM('First Year', 'Second Year', 'Third Year', 'Fourth Year'),
    school  ENUM('SAFS', 'SBE', 'SCI', 'SED', 'SCI', 'SEA' ,'SHS', 'SON', 'SPAS', 'Town Campus', 'Marimba', 'Mariene'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at DATETIME,
    reset_token VARCHAR(255),
    token_expires_at DATETIME,
    is_verified TINYINT(1) DEFAULT 0
);

-- ----------------------------
-- Table for the root admin
-- ----------------------------
CREATE TABLE root_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------
-- Table for Clubs
-- ------------------------------
CREATE TABLE clubs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Club name',
    description TEXT COMMENT 'Club description',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------
-- Table for Registration Requests
-- ------------------------------
CREATE TABLE registration_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(100) NOT NULL COMMENT 'User registration number',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    club_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
);

-- ------------------------------
-- Table for Club Memberships
-- ------------------------------
CREATE TABLE club_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    club_id INT NOT NULL,
    membership_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    UNIQUE (user_id, club_id)
);

-- ------------------------------
-- Table for Password Reset Requests
-- ------------------------------
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reset_token VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed', 'expired') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------
-- Table for Login Attempts
-- ------------------------------
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    successful BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Drop existing triggers
DROP TRIGGER IF EXISTS update_user_timestamp;
DROP TRIGGER IF EXISTS update_club_timestamp;
DROP TRIGGER IF EXISTS update_registration_requests_timestamp;
DROP TRIGGER IF EXISTS update_club_members_timestamp;
DROP TRIGGER IF EXISTS prevent_duplicate_club_membership;
DROP TRIGGER IF EXISTS log_login_attempts;
DROP TRIGGER IF EXISTS set_password_reset_default_status;

DELIMITER $$

-- Recreate updated triggers

-- Trigger to update 'updated_at' for users table
CREATE TRIGGER update_user_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.updated_at IS NULL OR NEW.updated_at != OLD.updated_at THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END $$

-- Trigger to update 'updated_at' for clubs table
CREATE TRIGGER update_club_timestamp
BEFORE UPDATE ON clubs
FOR EACH ROW
BEGIN
    IF NEW.updated_at IS NULL OR NEW.updated_at != OLD.updated_at THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END $$

-- Trigger to update 'updated_at' for registration_requests table
CREATE TRIGGER update_registration_requests_timestamp
BEFORE UPDATE ON registration_requests
FOR EACH ROW
BEGIN
    IF NEW.updated_at IS NULL OR NEW.updated_at != OLD.updated_at THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END $$

-- Trigger to update 'updated_at' for club_members table
CREATE TRIGGER update_club_members_timestamp
BEFORE UPDATE ON club_members
FOR EACH ROW
BEGIN
    IF NEW.updated_at IS NULL OR NEW.updated_at != OLD.updated_at THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END $$

-- Trigger to prevent a user from joining the same club twice
CREATE TRIGGER prevent_duplicate_club_membership
BEFORE INSERT ON club_members
FOR EACH ROW
BEGIN
    DECLARE club_count INT;

    SELECT COUNT(*) INTO club_count
    FROM club_members
    WHERE user_id = NEW.user_id AND club_id = NEW.club_id;

    IF club_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'User is already a member of this club.';
    END IF;
END $$

-- Trigger to log user login attempts
CREATE TRIGGER log_login_attempts
AFTER INSERT ON login_attempts
FOR EACH ROW
BEGIN
    INSERT INTO login_attempts_log (user_id, ip_address, attempt_time, successful)
    VALUES (NEW.user_id, NEW.ip_address, NEW.attempt_time, NEW.successful);
END $$

-- Trigger for Password Reset Requests (Set default status to 'pending')
CREATE TRIGGER set_password_reset_default_status
BEFORE INSERT ON password_resets
FOR EACH ROW
BEGIN
    IF NEW.status IS NULL THEN
        SET NEW.status = 'pending';
    END IF;
END $$

DELIMITER ;


