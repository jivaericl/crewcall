-- Drop calendar tables in correct order (reverse of dependencies)
-- Run this script to clean up partial migration

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `calendar_item_tag`;
DROP TABLE IF EXISTS `calendar_item_speaker`;
DROP TABLE IF EXISTS `calendar_item_client`;
DROP TABLE IF EXISTS `calendar_item_user`;
DROP TABLE IF EXISTS `calendar_items`;

SET FOREIGN_KEY_CHECKS = 1;

-- Also remove the failed migration record
DELETE FROM `migrations` WHERE `migration` = '2025_11_23_184058_create_calendar_items_table';
