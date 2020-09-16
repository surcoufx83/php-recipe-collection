<?php

namespace Surcouf\Cookbook\Database\Mapper;

if (!defined('CORE2'))
  exit;

interface TableMapperInterface {

  static function IdColumn() : string;
  static function TableName() : string;

}
