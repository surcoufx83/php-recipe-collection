<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

class DbSetup {

  static protected function execute(\Mysqli &$Database, string $query) : bool {
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static protected function finish(\Mysqli &$Database, int $version) : bool {
    $query = 'INSERT INTO `db_version`(`version_value`) VALUES ('.$version.')';
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static protected function reportError(\Mysqli &$Database, string $method, string $query) : void {
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

}
