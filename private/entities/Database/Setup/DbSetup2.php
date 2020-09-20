<?php

namespace Surcouf\Cookbook\Database\Setup;

use Surcouf\Cookbook\Database\DbConf;

if (!defined('CORE2'))
  exit;

final class DbSetup2 {

  static function install(\Mysqli &$Database) : bool {
    if (
      self::execute($Database, self::getQuery_createTable_cronjobs()) &&
      self::execute($Database, self::getQuery_createTable_cronjobs_log()) &&
      self::execute($Database, self::getQuery_createTable_tags()) &&
      self::execute($Database, self::getQuery_createTable_units()) &&
      self::execute($Database, self::getQuery_createTable_users()) &&
      self::execute($Database, self::getQuery_createTable_user_logins()) &&
      self::execute($Database, self::getQuery_createTable_recipes()) &&
      self::execute($Database, self::getQuery_createTable_recipe_ingredients()) &&
      self::execute($Database, self::getQuery_createTable_recipe_pictures()) &&
      self::execute($Database, self::getQuery_createTable_recipe_ratings()) &&
      self::execute($Database, self::getQuery_createTable_recipe_tags()) &&
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
    $query = 'INSERT INTO `db_version`(`version_value`) VALUES (2)';
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

  static private function getQuery_createTable_cronjobs() : string {
    return 'CREATE TABLE `cronjobs` (
    	`job_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`job_disabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
    	`job_name` VARCHAR(128) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`job_hour` VARCHAR(32) NOT NULL DEFAULT \'*\'COLLATE \'utf8mb4_general_ci\',
    	`job_minute` VARCHAR(32) NOT NULL DEFAULT \'*\'COLLATE \'utf8mb4_general_ci\',
    	`job_day` VARCHAR(32) NOT NULL DEFAULT \'*\'COLLATE \'utf8mb4_general_ci\',
    	`job_dow` VARCHAR(32) NOT NULL DEFAULT \'*\'COLLATE \'utf8mb4_general_ci\',
    	`job_month` VARCHAR(32) NOT NULL DEFAULT \'*\'COLLATE \'utf8mb4_general_ci\',
    	`job_maxcount` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	`job_command` VARCHAR(256) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`job_count` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT \'0\',
    	`job_lastrun` DATETIME NULL DEFAULT NULL,
    	`job_success` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
    	`last_runtime` DOUBLE NULL DEFAULT NULL,
    	`last_exception` MEDIUMTEXT NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`job_id`) USING BTREE,
    	INDEX `job_name` (`job_name`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_cronjobs_log() : string {
    return 'CREATE TABLE `cronjob_log` (
    	`entry_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`job_id` SMALLINT(5) UNSIGNED NOT NULL,
    	`entry_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	`entry_type` TINYINT(3) UNSIGNED NOT NULL,
    	`entry_payload` VARCHAR(1024) NOT NULL DEFAULT \'{ }\' COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	INDEX `FK_cronjob_log_cronjobs` (`job_id`) USING BTREE,
    	CONSTRAINT `FK_cronjob_log_cronjobs` FOREIGN KEY (`job_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`cronjobs` (`job_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_recipes() : string {
    return 'CREATE TABLE `recipes` (
    	`recipe_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	`recipe_public` TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\',
    	`recipe_name` VARCHAR(256) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_description` VARCHAR(1024) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_eater` TINYINT(3) UNSIGNED NOT NULL,
    	`recipe_source_desc` VARCHAR(1024) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_source_url` VARCHAR(256) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	`recipe_published` TIMESTAMP NULL DEFAULT NULL,
    	PRIMARY KEY (`recipe_id`) USING BTREE,
    	INDEX `FK_recipes_users` (`user_id`) USING BTREE,
    	CONSTRAINT `FK_recipes_users` FOREIGN KEY (`user_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_recipe_ingredients() : string {
    return 'CREATE TABLE `recipe_ingredients` (
    	`ingredient_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`recipe_id` INT(10) UNSIGNED NOT NULL,
    	`unit_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
    	`ingredient_quantity` DECIMAL(10,3) NULL DEFAULT NULL,
    	`ingredient_description` VARCHAR(128) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`ingredient_id`) USING BTREE,
    	INDEX `FK_recipe_ingredients_recipes` (`recipe_id`) USING BTREE,
    	INDEX `FK_recipe_ingredients_units` (`unit_id`) USING BTREE,
    	CONSTRAINT `FK_recipe_ingredients_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `cookbook`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_recipe_ingredients_units` FOREIGN KEY (`unit_id`) REFERENCES `cookbook`.`units` (`unit_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_recipe_pictures() : string {
    return 'CREATE TABLE `recipe_pictures` (
    	`picture_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`recipe_id` INT(10) UNSIGNED NOT NULL,
    	`user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	`picture_sortindex` TINYINT(3) UNSIGNED NOT NULL,
    	`picture_name` VARCHAR(128) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`picture_description` VARCHAR(1024) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`picture_hash` VARCHAR(32) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`picture_filename` VARCHAR(256) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`picture_full_path` VARCHAR(1024) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`picture_id`) USING BTREE,
    	UNIQUE INDEX `recipe_id_picture_sortindex` (`recipe_id`, `picture_sortindex`) USING BTREE,
    	INDEX `FK_recipe_pictures_users` (`user_id`) USING BTREE,
    	CONSTRAINT `FK_recipe_pictures_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `cookbook`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_recipe_pictures_users` FOREIGN KEY (`user_id`) REFERENCES `cookbook`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_recipe_ratings() : string {
    return 'CREATE TABLE `recipe_ratings` (
    	`entry_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_id` MEDIUMINT(8) UNSIGNED NOT NULL,
    	`recipe_id` INT(10) UNSIGNED NOT NULL,
    	`entry_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	`entry_comment` VARCHAR(1024) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`entry_viewed` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
    	`entry_cooked` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
    	`entry_vote` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT \'= voting 0 ... 5\',
    	`entry_rate` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT \'= difficulty 1 easy ... 3 hard\',
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	INDEX `FK_recipe_log_users` (`user_id`) USING BTREE,
    	INDEX `FK_recipe_log_recipes` (`recipe_id`) USING BTREE,
    	CONSTRAINT `FK_recipe_log_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `cookbook`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_recipe_log_users` FOREIGN KEY (`user_id`) REFERENCES `cookbook`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_recipe_tags() : string {
    return 'CREATE TABLE `recipe_tags` (
    	`entry_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`recipe_id` INT(10) UNSIGNED NOT NULL,
    	`tag_id` SMALLINT(5) UNSIGNED NOT NULL,
    	`user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	UNIQUE INDEX `recipe_id_tag_id_user_id` (`recipe_id`, `tag_id`, `user_id`) USING BTREE,
    	INDEX `FK_recipe_tags_tags` (`tag_id`) USING BTREE,
    	INDEX `FK_recipe_tags_users` (`user_id`) USING BTREE,
    	CONSTRAINT `FK_recipe_tags_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `cookbook`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_recipe_tags_tags` FOREIGN KEY (`tag_id`) REFERENCES `cookbook`.`tags` (`tag_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_recipe_tags_users` FOREIGN KEY (`user_id`) REFERENCES `cookbook`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_tags() : string {
    return 'CREATE TABLE `tags` (
    	`tag_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`tag_name` VARCHAR(32) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`tag_id`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_units() : string {
    return 'CREATE TABLE `units` (
    	`unit_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`unit_name` VARCHAR(16) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`unit_id`) USING BTREE,
    	UNIQUE INDEX `unit_name` (`unit_name`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_users() : string {
    return 'CREATE TABLE `users` (
    	`user_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_name` VARCHAR(64) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`oauth_user_name` VARCHAR(32) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`user_firstname` VARCHAR(64) NOT NULL DEFAULT \'\' COLLATE \'utf8mb4_general_ci\',
    	`user_lastname` VARCHAR(64) NOT NULL DEFAULT \'\' COLLATE \'utf8mb4_general_ci\',
    	`user_fullname` VARCHAR(128) NOT NULL DEFAULT \'\' COLLATE \'utf8mb4_general_ci\',
    	`user_hash` VARCHAR(32) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`user_isadmin` TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\',
    	`user_password` VARCHAR(256) NOT NULL DEFAULT \'\' COLLATE \'utf8mb4_general_ci\',
    	`user_email` VARCHAR(256) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`user_email_validation` VARCHAR(256) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`user_email_validated` DATETIME NULL DEFAULT NULL,
    	`user_last_activity` DATETIME NULL DEFAULT NULL,
    	`user_avatar` VARCHAR(40) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`user_registration_completed` DATETIME NULL DEFAULT NULL,
    	`user_adconsent` DATETIME NULL DEFAULT NULL,
    	PRIMARY KEY (`user_id`) USING BTREE,
    	UNIQUE INDEX `user_email` (`user_email`) USING BTREE,
    	UNIQUE INDEX `user_name` (`user_name`) USING BTREE,
    	UNIQUE INDEX `oauth_user_name` (`oauth_user_name`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_user_logins() : string {
    return 'CREATE TABLE `user_logins` (
    	`login_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_id` MEDIUMINT(8) UNSIGNED NOT NULL,
    	`login_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	`login_token` VARCHAR(128) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`login_password` VARCHAR(128) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`login_keep` TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\',
    	`login_oauthdata` TEXT NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`login_id`) USING BTREE,
    	INDEX `user_id` (`user_id`) USING BTREE,
    	INDEX `login_time` (`login_time`) USING BTREE,
    	INDEX `login_keep` (`login_keep`) USING BTREE,
    	CONSTRAINT `FK_user_logins_users` FOREIGN KEY (`user_id`) REFERENCES `'.DbConf::DB_DATABASE.'`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

}
