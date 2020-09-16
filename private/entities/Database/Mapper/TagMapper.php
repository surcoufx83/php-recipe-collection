<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class TagMapper implements TableMapperInterface {

  static $TableName = 'tags';
  static $IdColumn = 'tag_id';

  static function IdColumn() : string {
    return TagMapper::$IdColumn;
  }

  static function TableName() : string {
    return TagMapper::$TableName;
  }

}
