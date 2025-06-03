-- Add profile_image column to pengguna table if it doesn't exist
ALTER TABLE `pengguna` 
ADD COLUMN IF NOT EXISTS `profile_image` VARCHAR(255) DEFAULT 'user_file/default.png' AFTER `no_hp`;

-- Update existing records to have the default profile image path
UPDATE `pengguna` SET `profile_image` = 'user_file/default.png' WHERE `profile_image` IS NULL;
