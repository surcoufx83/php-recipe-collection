<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class UnitMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'units';
    $this->IdColumn = 'unit_id';
    $this->NameColumn = 'unit_name';
  }

}
