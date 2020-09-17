<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

final class UserMapper extends Mapper implements TableMapperInterface {

  public function __construct() {
    $this->TableName = 'users';
    $this->IdColumn = 'user_id';
    $this->NameColumn = 'user_name';
  }

}
