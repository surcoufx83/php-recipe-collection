<?php

namespace Surcouf\Cookbook\Database\Setup;

use Surcouf\Cookbook\Database\DbConf;

if (!defined('CORE2'))
  exit;

final class DbSetup3 {

  static function install(\Mysqli &$Database) : bool {
    if (
      self::execute($Database, self::getQuery_createTable_activities()) &&
      self::finish($Database)
      )
    return true;
    return false;
  }

  static private function execute(\Mysqli &$Database, string $query) : bool {
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static private function finish(\Mysqli &$Database) : bool {
    $query = 'INSERT INTO `db_version`(`version_value`) VALUES (3)';
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static private function reportError(\Mysqli &$Database, string $method, string $query) : void {
    $query = 'INSERT INTO `db_logs`(`entry_code`, `entry_message`, `entry_file`, `entry_method`, `entry_query`) VALUES
      ('.$Database->errno.',
       \''.$Database->real_escape_string($Database->error).'\',
       \''.$Database->real_escape_string(__FILE__).'\',
       \''.$Database->real_escape_string($method).'\',
       \''.$Database->real_escape_string($query).'\')';
    $Database->rollback();
    $Database->autocommit(true);
    $Database->query($query);
  }

  static private function getQuery_createTable_activities() : string {
    return 'CREATE TABLE `activities` (
    	`entry_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	`entry_timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    	`entry_type` TINYINT(3) UNSIGNED NOT NULL,
    	`entry_data` VARCHAR(1024) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`picture_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`rating_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`tag_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	INDEX `entry_timestamp` (`entry_timestamp`) USING BTREE,
    	INDEX `FK_activities_users` (`user_id`) USING BTREE,
    	INDEX `FK_activities_recipes` (`recipe_id`) USING BTREE,
    	INDEX `FK_activities_recipe_pictures` (`picture_id`) USING BTREE,
    	INDEX `FK_activities_recipe_ratings` (`rating_id`) USING BTREE,
    	INDEX `FK_activities_recipe_tags` (`tag_id`) USING BTREE,
    	CONSTRAINT `FK_activities_recipe_pictures` FOREIGN KEY (`picture_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`recipe_pictures` (`picture_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_recipe_ratings` FOREIGN KEY (`rating_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`recipe_ratings` (`entry_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_activities_recipe_tags` FOREIGN KEY (`tag_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`recipe_tags` (`entry_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_users` FOREIGN KEY (`user_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

}
