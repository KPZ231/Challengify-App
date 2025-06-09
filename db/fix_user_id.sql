-- Fix user with empty ID
UPDATE `users` 
SET `id` = 'e483f9d7-c3e8-4d20-a93e-6debcbbaec70' 
WHERE `id` = '' AND `username` = 'admin2';

-- If you prefer to delete the user instead, uncomment below:
-- DELETE FROM `users` WHERE `id` = '' AND `username` = 'admin2'; 