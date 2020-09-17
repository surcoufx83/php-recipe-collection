<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class RatingMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'recipe_ratings';
    $this->IdColumn = 'entry_id';
    $this->NameColumn = null;
  }

}
