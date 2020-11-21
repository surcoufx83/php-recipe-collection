<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup6 extends DbSetup {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      parent::execute($Database, self::getQuery_updateUsers_clearOAuthMail($dbname)) &&
      parent::finish($Database, 6)
      )
      return true;
    return false;
  }

  static private function getQuery_updateUsers_clearOAuthMail(string $dbname) : string {
    return 'UPDATE `'.$dbname.'`.`users` SET `user_email`=\'\' WHERE `user_email` LIKE \'OAuth2::%\'';
  }

}
