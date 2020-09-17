<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

interface TableMapperInterface {

  public function IdColumn() : string;
  public function NameColumn() : string;
  public function NameSearch() : bool;
  public function TableName() : string;

}
