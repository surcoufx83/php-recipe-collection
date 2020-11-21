<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup6 extends DbSetup {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      parent::execute($Database, self::getQuery_updateUsers_removeMailKey()) &&
      parent::execute($Database, self::getQuery_updateUsers_clearOAuthMail($dbname)) &&
      parent::finish($Database, 6)
      )
      return true;
    return false;
  }

  static private function getQuery_updateUsers_removeMailKey() : string {
    return 'ALTER TABLE `users`
    	CHANGE COLUMN `user_email` `user_email` VARCHAR(256) NULL COLLATE \'utf8mb4_general_ci\' AFTER `user_password`,
    	DROP INDEX `user_email`';
  }

  static private function getQuery_updateUsers_clearOAuthMail(string $dbname) : string {
    return 'UPDATE `'.$dbname.'`.`users` SET `user_email`=\'\' WHERE `user_email` LIKE \'OAuth2::%\'';
  }

}
