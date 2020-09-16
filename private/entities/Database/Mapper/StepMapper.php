<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class StepMapper implements TableMapperInterface {

  static $TableName = 'recipe_steps';
  static $IdColumn = 'step_id';

  static function IdColumn() : string {
    return StepMapper::$IdColumn;
  }

  static function TableName() : string {
    return StepMapper::$TableName;
  }

}
