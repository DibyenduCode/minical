-- MiniCal Database Schema
-- Database Name: minical

CREATE DATABASE IF NOT EXISTS `minical` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `minical`;

-- Drop tables if they exist to allow clean imports
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `plans`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `api_tokens`;
DROP TABLE IF EXISTS `calendar_events`;
DROP TABLE IF EXISTS `google_accounts`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `payment_accounts`;
DROP TABLE IF EXISTS `booking_form_responses`;
DROP TABLE IF EXISTS `booking_form_fields`;
DROP TABLE IF EXISTS `booking_logs`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `availability`;
DROP TABLE IF EXISTS `profiles`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- Users Table
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Profiles Table
CREATE TABLE `profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `phone` VARCHAR(30) NULL,
  `timezone` VARCHAR(50) NOT NULL DEFAULT 'UTC',
  `custom_domain` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `avatar_url` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_profile` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Availability Table (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
CREATE TABLE `availability` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `day_of_week` TINYINT UNSIGNED NOT NULL COMMENT '0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat',
  `start_time` TIME NOT NULL DEFAULT '09:00:00',
  `end_time` TIME NOT NULL DEFAULT '17:00:00',
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_day_unique` (`user_id`, `day_of_week`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Types / Consultation Services Table
CREATE TABLE `events` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `duration_minutes` INT NOT NULL DEFAULT 30,
  `booking_window_days` INT NOT NULL DEFAULT 30,
  `location_type` ENUM('online', 'phone', 'in_person') NOT NULL DEFAULT 'online',
  `is_paid` TINYINT(1) NOT NULL DEFAULT 0,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings Table
CREATE TABLE `bookings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `event_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `booking_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `status` ENUM('pending', 'awaiting_payment', 'paid', 'confirmed', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'confirmed',
  `google_event_id` VARCHAR(255) NULL,
  `cancellation_reason` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Booking Logs Table
CREATE TABLE `booking_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `note` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Custom Booking Form Fields Table
CREATE TABLE `booking_form_fields` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `event_id` INT UNSIGNED NULL,
  `label` VARCHAR(150) NOT NULL,
  `field_type` ENUM('text', 'textarea', 'email', 'phone', 'number', 'date', 'select', 'radio', 'checkbox', 'yes_no', 'url') NOT NULL DEFAULT 'text',
  `options` TEXT NULL COMMENT 'JSON array for select/radio/checkbox options',
  `placeholder` VARCHAR(255) NULL,
  `help_text` VARCHAR(255) NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 0,
  `display_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Booking Form Responses Table
CREATE TABLE `booking_form_responses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL,
  `field_id` INT UNSIGNED NULL,
  `field_label` VARCHAR(150) NOT NULL,
  `value` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`field_id`) REFERENCES `booking_form_fields`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Accounts Table
CREATE TABLE `payment_accounts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `gateway` ENUM('stripe', 'razorpay') NOT NULL,
  `api_key` VARCHAR(255) NOT NULL,
  `api_secret` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments Table
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `gateway` VARCHAR(50) NOT NULL,
  `transaction_id` VARCHAR(150) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('pending', 'success', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Google Accounts Table
CREATE TABLE `google_accounts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `google_email` VARCHAR(150) NOT NULL,
  `access_token` TEXT NOT NULL,
  `refresh_token` TEXT NOT NULL,
  `token_expires_at` TIMESTAMP NULL,
  `calendar_id` VARCHAR(150) DEFAULT 'primary',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Calendar Events Table
CREATE TABLE `calendar_events` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL UNIQUE,
  `google_event_id` VARCHAR(255) NOT NULL,
  `synced_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- API Tokens Table
CREATE TABLE `api_tokens` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `token` VARCHAR(255) NOT NULL UNIQUE,
  `expires_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password Resets Table
CREATE TABLE `password_resets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System Settings Table
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (Password: admin123)
-- Hash generated via password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password_hash`, `role`, `status`) VALUES
(1, 'Admin User', 'admin', 'admin@minical.local', '$2y$10$1whQ4HiR7NOpUWq0XkD1eeH3/9ax7C2i9UG0XgpzksDzxyXkZxqz.', 'admin', 'active');

-- Insert Default Profile for Admin
INSERT INTO `profiles` (`user_id`, `phone`, `timezone`, `bio`) VALUES
(1, '+1234567890', 'UTC', 'System Administrator');

-- Insert Default Event for Admin
INSERT INTO `events` (`id`, `user_id`, `name`, `slug`, `description`, `duration_minutes`, `location_type`, `is_paid`, `price`, `currency`, `status`) VALUES
(1, 1, '30 Minute Meeting', '30-min-meeting', 'A 30-minute introductory or sync meeting.', 30, 'online', 0, 0.00, 'USD', 'active');

-- Insert Default Availability (Mon-Fri 09:00 to 17:00)
INSERT INTO `availability` (`user_id`, `day_of_week`, `start_time`, `end_time`, `is_enabled`) VALUES
(1, 0, '09:00:00', '17:00:00', 0), -- Sunday (disabled)
(1, 1, '09:00:00', '17:00:00', 1), -- Monday
(1, 2, '09:00:00', '17:00:00', 1), -- Tuesday
(1, 3, '09:00:00', '17:00:00', 1), -- Wednesday
(1, 4, '09:00:00', '17:00:00', 1), -- Thursday
(1, 5, '09:00:00', '17:00:00', 1), -- Friday
(1, 6, '09:00:00', '17:00:00', 0); -- Saturday (disabled)

-- Insert Default System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'MiniCal'),
('smtp_host', 'smtp.mailtrap.io'),
('smtp_port', '2525'),
('smtp_user', ''),
('smtp_pass', '');

-- Insert Real Available Features Pricing Plans
INSERT INTO `plans` (`name`, `slug`, `price`, `billing_cycle`, `description`, `features`, `badge`, `button_text`, `is_featured`, `display_order`) VALUES
('Free', 'free', '$0', 'forever', 'Everything you need for individual appointment scheduling.', '["1 user account & custom link (/u/username)", "Unlimited event types & durations", "Weekly working hours & availability manager", "Custom booking form builder", "Bilingual support (English & Bengali)"]', 'Free forever', 'Get Started', 0, 1),
('Growth', 'growth', '$9', 'per month', 'For active hosts needing calendar sync and paid appointments.', '["Includes all Free features", "Google Calendar synchronization", "Accept paid bookings (Stripe & Razorpay)", "Custom booking form response logs", "Email & SMTP notification delivery"]', 'Popular', 'Start Trial', 0, 2),
('Pro', 'pro', '$19', 'per month', 'Full access to API endpoints and advanced scheduling controls.', '["Includes all Growth features", "REST API (v1) access with Bearer Token", "Mobile app integration support", "Priority appointment slot generator", "Detailed revenue & booking analytics"]', 'Most Popular', 'Get Pro Access', 1, 3),
('Super Admin', 'super-admin', 'Custom', 'platform license', 'Complete system control and administration features.', '["Dedicated Super Admin Control Panel", "Global user account management & status controls", "Global appointments log across all hosts", "Custom site title & SMTP host configurations", "Full database & server control"]', 'Full System Control', 'Contact Admin', 0, 4);
