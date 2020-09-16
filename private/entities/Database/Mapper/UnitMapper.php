<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class UnitMapper implements TableMapperInterface {

  static $TableName = 'units';
  static $IdColumn = 'unit_id';

  static function IdColumn() : string {
    return UnitMapper::$IdColumn;
  }

  static function TableName() : string {
    return UnitMapper::$TableName;
  }

}
