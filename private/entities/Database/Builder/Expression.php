<?php

namespace Surcouf\Cookbook\Database\Builder;

use Surcouf\Cookbook\Helper\Flags;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Database\EQueryType;

if (!defined('CORE2'))
  exit;

class Expression {

  private $parent;
  private $braces = false;
  private $items = [];

  public function __construct($parent = null) {
    $this->parent = $parent;
  }

  public function __toString() : String {
    var_dump($this->items);
    for ($i=0; $i<count($this->items); $i++) {
      var_dump($i, $this->items[$i]);
      if ($i > 0 && $i % 2 == 0)
        $this->items[$i] = $this->formattype($this->items[$i]);
      else
        $this->items[$i] = $this->formatitem($this->items[$i]);
    }
    $str = join(' ', $this->items);
    if ($this->braces)
      $str = '('.$str.')';
    return $str;
  }

  public function ret() {
    return $this->parent;
  }

  private function expr(int $type, array $params) : void {
    $this->items[] = array_merge(['type' => $type], $params);
  }

  private function formatitem($item) : string {
    if (is_a($item, Expression::class))
      return ''.$item;
    return join(' ', [
      $this->format1item($item['left']),
      $this->formattype($item['type']),
      $this->format1item($item['right']),
    ]);
  }

  private function format1item($item) : string {
    global $Controller;
    if (is_array($item) && array_key_exists('table', $item) && array_key_exists('column', $item))
      return QueryBuilder::maskField($item['table'], QueryBuilder::getTableAlias($item['table']), $item['column']);
    return $Controller->dbescape($item);
  }

  private function formattype(int $type) : string {
    return EExpressionType::getString($type);
  }

  public function braces() : Expression {
    $this->braces = !$this->braces;
    return $this;
  }

  public function e(array $params, bool $returnSelf = false) {
    $this->items[] = $params['expression'];
    return $returnSelf ? $this : $this->parent;
  }

  public function equals(array $params, bool $returnSelf = false) {
    $this->expr(EExpressionType::etEQUALS, $params);
    return $returnSelf ? $this : $this->parent;
  }

  public function is(array $params, bool $returnSelf = false) {
    $this->expr(EExpressionType::etIS, $params);
    return $returnSelf ? $this : $this->parent;
  }

  public function and(bool $returnSelf = true) {
    $this->items[] = EExpressionType::etAND;
    return $returnSelf ? $this : $this->parent;
  }

  public function or(bool $returnSelf = true) {
    $this->items[] = EExpressionType::etOR;
    return $returnSelf ? $this : $this->parent;
  }

}
