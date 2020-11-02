<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup1 {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      self::execute($Database, self::getQuery_createTable_db_logs()) &&
      self::execute($Database, self::getQuery_createTable_db_versions()) &&
      self::finish($Database)
      )
      return true;
    return false;
  }

  static private function execute(\Mysqli &$Database, string $query) : bool {
    $result = $Database->query($query);
    return $result;
  }

  static private function finish(\Mysqli &$Database) : bool {
    $query = 'INSERT INTO `db_version`(`version_value`) VALUES (1)';
    $result = $Database->query($query);
    return $result;
  }

  static private function reportError(\Mysqli &$Database, string $method, string $query) : void { }

  static private function getQuery_createTable_db_logs() : string {
    return 'CREATE TABLE `db_logs` (
    	`entry_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`entry_file` VARCHAR(1024) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`entry_method` VARCHAR(256) NOT NULL COLLATE \'utf8mb4_general_ci\',
    	`entry_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	`entry_code` SMALLINT(5) UNSIGNED NOT NULL,
    	`entry_message` VARCHAR(2048) NOT NULL COLLATE \'utf8mb4_general_ci\',
      `entry_query` TEXT NOT NULL DEFAULT \'\' COLLATE \'utf8mb4_general_ci\',
    	PRIMARY KEY (`entry_id`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createTable_db_versions() : string {
    return 'CREATE TABLE `db_version` (
    	`entry_id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`version_value` TINYINT(3) UNSIGNED NOT NULL,
    	`version_installed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	UNIQUE INDEX `version_value` (`version_value`) USING BTREE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

}
