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
    for ($i=0; $i<count($this->items); $i++) {
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
    if (\array_key_exists('left', $item) && \array_key_exists('type', $item) && \array_key_exists('right', $item))
      return join(' ', [
        $this->format1item($item['left']),
        $this->formattype($item['type']),
        $this->format1item($item['right']),
      ]);
    if (\array_key_exists('left', $item) && \array_key_exists('type', $item))
      return join(' ', [
        $this->format1item($item['left']),
        $this->formattype($item['type']),
      ]);
    if (\array_key_exists('type', $item))
      return $this->formattype($item['type']);
    throw new \Exception("Error Processing Request Expression::formatitem", 1);

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

  public function e() : Expression {
    $i = count($this->items);
    $this->items[$i] = new Expression($this);
    return $this->items[$i];
  }

  public function contains(array $params, bool $returnSelf = false) {
    $params['right'] = '%'.$params['right'].'%';
    $this->expr(EExpressionType::etCONTAINS, $params);
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
    $this->expr(EExpressionType::etAND, []);
    return $returnSelf ? $this : $this->parent;
  }

  public function or(bool $returnSelf = true) {
    $this->expr(EExpressionType::etOR, []);
    return $returnSelf ? $this : $this->parent;
  }

}
