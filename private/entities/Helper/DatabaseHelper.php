<?php

namespace Surcouf\Cookbook\Helper;

use Mysqli;
use Mysqli_result;
use Surcouf\Cookbook\Database\QueryBuilder;

if (!defined('CORE2'))
  exit;

final class DatabaseHelper implements DatabaseHelperInterface {

  public static function select(Mysqli &$db, QueryBuilder &$queryBuilder) : ?mysqli_result {
    $query = $queryBuilder->buildQuery();
    $result = $db->query($query);
    if (!is_a($result, 'mysqli_result'))
      return null;
    return $result;
  }

  public static function selectFirst(Mysqli &$db, QueryBuilder &$queryBuilder) : ?array {
    $query = $queryBuilder->buildQuery();
    $result = DatabaseHelper::select($db, $queryBuilder);
    if (!is_null($result) && $result->num_rows > 0)
      return $result->fetch_assoc();
    return null;
  }

  public static function selectObject(Mysqli &$db, QueryBuilder &$queryBuilder, string $className) : ?object {
    $query = $queryBuilder->buildQuery();
    $result = DatabaseHelper::select($db, $queryBuilder);
    if (!is_null($result) && $result->num_rows > 0)
      return $result->fetch_object($className);
    return null;
  }

}
