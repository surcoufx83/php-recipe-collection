<?php

namespace Surcouf\Cookbook\Database;

if (!defined('CORE2'))
  exit;

final class Setup {

  static $LatestVersion = 2;

  static function checkAndPatch(\Mysqli &$Database) {
    if (!self::simpleTest())
      self::fullTestAndUpdate($Database);
  }

  static private function finishUpdate() : void {
    foreach (glob(__DIR__.DS.'db?*') as $filename) {
      unlink($filename);
    }
    touch(__DIR__.DS.'db'.self::$LatestVersion);
  }

  static private function fullTestAndUpdate(\Mysqli &$Database) : void {
    $query = 'SELECT *
              FROM `information_schema`.`tables`
              WHERE `table_schema`=\''.DbConf::DB_DATABASE.'\' AND `table_name`=\'db_version\'';
    $result = $Database->query($query);
    if ($result->num_rows == 0)
      $version = 0;
    else {
      $query = 'SELECT MAX(`version_value`) AS `version`
                FROM `db_version`';
      $result = $Database->query($query);
      if ($result->num_rows == 0)
        $version = 0;
      $version = intval(($result->fetch_assoc())['version']);
    }
    if ($version == self::$LatestVersion)
      self::finishUpdate();
    for ($i = $version; $i<self::$LatestVersion; $i++) {
      $class = 'Surcouf\Cookbook\Database\Setup\DbSetup'.($i + 1);
      $Database->autocommit(false);
      $Database->begin_transaction();
      if (!$class::install($Database)) {
        var_dump($Database);
        throw new \Exception('Error updating database to version '.($i + 1).'.', 1);
      }
      $result = $Database->commit();
      if ($result == false) {
        $Database->rollback();
      }
      $Database->autocommit(true);
    }
  }

  static private function simpleTest() : bool {
    return file_exists(__DIR__.DS.'db'.self::$LatestVersion);
  }

}
