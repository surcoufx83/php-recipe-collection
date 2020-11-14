<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup5 extends DbSetup {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      parent::execute($Database, self::getQuery_updatePictures_addUploaded()) &&
      parent::finish($Database, 5)
      )
      return true;
    return false;
  }

  static private function getQuery_updatePictures_addUploaded() : string {
    return 'ALTER TABLE `recipe_pictures`
	    ADD COLUMN `picture_uploaded` TIMESTAMP DEFAULT CURRENT_TIMESTAMP() AFTER `picture_full_path`';
  }

}
