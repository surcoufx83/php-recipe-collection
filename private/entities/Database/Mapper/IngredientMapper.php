<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class IngredientMapper implements TableMapperInterface {

  static $TableName = 'recipe_ingredients';
  static $IdColumn = 'ingredient_id';

  static function IdColumn() : string {
    return IngredientMapper::$IdColumn;
  }

  static function TableName() : string {
    return IngredientMapper::$TableName;
  }

}
