<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class StepMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'recipe_steps';
    $this->IdColumn = 'step_id';
    $this->NameColumn = null;
  }

}
