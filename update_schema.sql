-- Add profile_image column to the pengguna table
ALTER TABLE pengguna ADD COLUMN profile_image VARCHAR(255) DEFAULT 'user_file/default.png';

-- Update existing users to have the default profile image
UPDATE pengguna SET profile_image = 'user_file/default.png' WHERE profile_image IS NULL;
