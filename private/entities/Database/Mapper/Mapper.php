<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

class Mapper implements TableMapperInterface {

  protected $TableName = null;
  protected $IdColumn = null;
  protected $NameColumn = null;

  public function IdColumn() : string {
    return $this->IdColumn;
  }

  public function NameColumn() : string {
    return $this->NameColumn;
  }

  public function NameSearch() : bool {
    return !is_null($this->NameColumn);
  }

  public function TableName() : string {
    return $this->TableName;
  }

}
