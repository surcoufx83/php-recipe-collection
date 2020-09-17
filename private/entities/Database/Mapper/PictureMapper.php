<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class PictureMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'recipe_pictures';
    $this->IdColumn = 'picture_id';
    $this->NameColumn = null;
  }

}
