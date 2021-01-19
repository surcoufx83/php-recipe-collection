<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup6 extends DbSetup {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      parent::execute($Database, self::getQuery_updatePictures_addProportions()) &&
      parent::finish($Database, 6)
      )
      return true;
    return false;
  }

  static private function getQuery_updatePictures_addProportions() : string {
    return 'ALTER TABLE `recipe_pictures`
    	ADD COLUMN `picture_width` SMALLINT NULL DEFAULT NULL AFTER `picture_uploaded`,
    	ADD COLUMN `picture_height` SMALLINT NULL DEFAULT NULL AFTER `picture_width`';
  }

}
