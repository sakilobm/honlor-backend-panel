-- Create Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(64) NOT NULL UNIQUE,
    `permissions` LONGTEXT COMMENT 'JSON Permission Matrix',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Alter Auth Table
ALTER TABLE `auth` 
ADD COLUMN `role_id` INT DEFAULT NULL,
ADD COLUMN `is_master` TINYINT(1) DEFAULT 0;

-- Set Sakil as Master Admin
UPDATE `auth` SET `is_master` = 1 WHERE `username` = 'sakil';

-- Fix Sakil Profile if missing
INSERT INTO `profiles` (`id`, `firstname`, `lastname`, `created_at`)
SELECT 51, 'Sakil', 'Admin', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `profiles` WHERE `id` = 51);

-- Insert Default Super Role
INSERT INTO `roles` (`name`, `permissions`) VALUES ('Super Admin', '{"all": true}');
