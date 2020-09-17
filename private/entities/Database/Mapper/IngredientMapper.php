<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class IngredientMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'recipe_ingredients';
    $this->IdColumn = 'ingredient_id';
    $this->NameColumn = null;
  }

}
