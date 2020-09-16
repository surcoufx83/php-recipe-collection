<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class RecipeMapper implements TableMapperInterface {

  static $TableName = 'recipes';
  static $IdColumn = 'recipe_id';

  static function IdColumn() : string {
    return RecipeMapper::$IdColumn;
  }

  static function TableName() : string {
    return RecipeMapper::$TableName;
  }

}
