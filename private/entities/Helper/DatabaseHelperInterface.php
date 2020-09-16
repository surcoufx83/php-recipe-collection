<?php

namespace Surcouf\Cookbook\Helper;

use Mysqli;
use Mysqli_result;
use Surcouf\Cookbook\Database\QueryBuilder;

if (!defined('CORE2'))
  exit;

interface DatabaseHelperInterface {

  public static function select(Mysqli &$db, QueryBuilder &$queryBuilder) : ?mysqli_result;
  public static function selectFirst(Mysqli &$db, QueryBuilder &$queryBuilder) : ?array;
  public static function selectObject(Mysqli &$db, QueryBuilder &$queryBuilder, string $className) : ?object;

}
