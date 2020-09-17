<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class RecipeMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'recipes';
    $this->IdColumn = 'recipe_id';
    $this->NameColumn = null;
  }

}
