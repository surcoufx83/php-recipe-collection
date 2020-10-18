<?php

namespace Surcouf\Cookbook\Database\Builder;

use Surcouf\Cookbook\Helper\Flags;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Database\Builder\Expression;

if (!defined('CORE2'))
  exit;

class WhereCondition {

  private $builder;
  private $conditions = [];

  public function __construct(QueryBuilder $builder) {
    $this->builder = $builder;
  }

  public function __toString() : String {
    return join(' ', $this->conditions);
  }

  public function ret() : QueryBuilder {
    return $this->builder;
  }

  public function expr(?bool $or = false) : Expression {
    $i = count($this->conditions);
    if ($i > 0) {
      $this->conditions[] = $or ? 'OR' : 'AND';
      $i++;
    }
    $this->conditions[$i] = new Expression($this);
    return $this->conditions[$i];
  }

  public function and() : Expression {
    return $this->expr(false);
  }

  public function or() : Expression {
    return $this->expr(true);
  }

}
