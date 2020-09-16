<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class RatingMapper implements TableMapperInterface {

  static $TableName = 'recipe_ratings';
  static $IdColumn = 'entry_id';

  static function IdColumn() : string {
    return RatingMapper::$IdColumn;
  }

  static function TableName() : string {
    return RatingMapper::$TableName;
  }

}
