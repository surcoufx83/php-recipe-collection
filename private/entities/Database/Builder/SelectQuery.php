<?php

namespace Surcouf\Cookbook\Database\Builder;

use Surcouf\Cookbook\Helper\Flags;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Database\EQueryType;

if (!defined('CORE2'))
  exit;

class SelectQuery extends QueryBuilder {

  public function __construct(string $table = null, $selector = null) {
    $this->queryType = EQueryType::qtSELECT;
    $this->table($table);
    if (!is_null($selector) && $queryType == EQueryType::qtSELECT)
      $this->select($table, $selector);
  }

}
