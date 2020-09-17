<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class TagMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'tags';
    $this->IdColumn = 'tag_id';
    $this->NameColumn = 'tag_name';
  }

}
