<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class PictureMapper implements TableMapperInterface {

  static $TableName = 'recipe_pictures';
  static $IdColumn = 'picture_id';

  static function IdColumn() : string {
    return PictureMapper::$IdColumn;
  }

  static function TableName() : string {
    return PictureMapper::$TableName;
  }

}
