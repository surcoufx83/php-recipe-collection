<?php

namespace Surcouf\Cookbook\Database;

use Surcouf\Cookbook\Helper\Flags;
use Surcouf\Cookbook\IController;

if (!defined('CORE2'))
  exit;

define('DB_ANY', '*');

class QueryBuilder {

  private $queryType;

  private $primaryTable;
  private $selectors = array();
  private $updates = array();
  private $joinedTables = array();
  private $conditions = array();
  private $groups = array();
  private $orders = array();
  private $limitstart = -1;
  private $limitlen = -1;
  private $columns = array();
  private $values = array();

  private $aliases = array();

  public function __construct(int $queryType = EQueryType::None, string $table = null, $selector = null) {
    $this->queryType = $queryType;
    if (!is_null($table))
      $this->table($table);
    if (!is_null($selector) && $queryType == EQueryType::qtSELECT)
      $this->select($table, $selector);
    else if (!is_null($selector) && $queryType == EQueryType::qtINSERT)
      $this->columns($selector);
  }

  public function buildStmt() : string {

  }

  public function buildQuery() : string {

    if ($this->queryType == EQueryType::None)
      throw new \Exception('Undefined query type.');

    if (Flags::has_flag($this->queryType, EQueryType::qtPREPARED_STMT))
      return $this->buildStmt();

    if ($this->queryType == EQueryType::qtSELECT)
      return $this->buildSelectQuery();

    if ($this->queryType == EQueryType::qtUPDATE)
      return $this->buildUpdateQuery();

    if ($this->queryType == EQueryType::qtDELETE)
      return $this->buildDeleteQuery();

    if ($this->queryType == EQueryType::qtINSERT)
      return $this->buildInsertQuery();

    throw new \Exception("Not yet implemented");


  }

  private function buildDeleteQuery() : string {
    global $Controller;

    if ($this->limitlen <= 0)
      throw new \Exception('No limit set.');
    if (count($this->conditions) == 0)
      throw new \Exception('No conditions set.');

    $tbl = $this->maskstr($this->primaryTable);
    return 'DELETE FROM '.$tbl.' WHERE '.join(' ', $this->conditions);
  }

  private function buildInsertQuery() : string {
    global $Controller;

    if (count($this->columns) == 0)
      throw new \Exception('No columns set.');
    if (count($this->values) == 0)
      throw new \Exception('No values provided.');

    $cols = array();
    for ($i=0; $i<count($this->columns); $i++) {
      $cols[] = $this->maskstr($this->columns[$i]);
    }
    $vals = array();
    for ($i=0; $i<count($this->values); $i++) {
      $valline = array();
      for ($j=0; $j<count($this->values[$i]); $j++) {
        if (is_string($this->values[$i][$j]))
          $valline[] = $Controller->dbescape($this->values[$i][$j]);
        else if (is_integer($this->values[$i][$j]) || is_float($this->values[$i][$j]))
          $valline[] = $this->values[$i][$j];
        else if (is_bool($this->values[$i][$j]))
          $valline[] = intval($this->values[$i][$j]);
        else if (is_null($this->values[$i][$j]))
          $valline[] = 'NULL';
        else
          throw new \Exception('NYI.');
      }
      $vals[] = '('.join(', ', $valline).')';
    }

    $tbl = $this->maskstr($this->primaryTable);
    return 'INSERT INTO '.$tbl.'('.join(', ', $cols).') VALUES '.join(', ', $vals);
  }

  private function buildSelectQuery() : string {

    if (count($this->selectors) == 0)
      throw new \Exception('No selector given.');

    $sel = array();
    foreach ($this->selectors as $key => $value) {
      $alias = $this->aliases[$key];
      for ($i=0; $i<count($value); $i++) {
        $sel[] = $value[$i];
      }
    }
    $tbl = $this->maskstr($this->primaryTable, $this->aliases[$this->primaryTable]);
    $joinsexpr = (count($this->joinedTables) != 0 ? ' '.join(' ', $this->joinedTables) : '');
    $condexpr = (count($this->conditions) != 0 ? ' WHERE '.join(' ', $this->conditions) : '');
    $groupexpr = (count($this->groups) != 0 ? ' GROUP BY '.join(', ', $this->groups) : '');
    $orderexpr = (count($this->orders) != 0 ? ' ORDER BY '.join(', ', $this->orders) : '');
    $limitexpr = '';
    if ($this->limitlen > 0) {
      if ($this->limitstart > -1)
        $limitexpr = ' LIMIT '.$this->limitstart.', '.$this->limitlen;
      else
        $limitexpr = ' LIMIT '.$this->limitlen;
    }

    return 'SELECT '.join(', ', $sel).' FROM '.$tbl.$joinsexpr.$condexpr.$groupexpr.$orderexpr.$limitexpr;
  }

  private function buildUpdateQuery() : string {
    global $Controller;

    if (count($this->updates) == 0)
      throw new \Exception('No updated fields given.');
    if (count($this->conditions) == 0)
      throw new \Exception('No conditions for update given.');

    $update = array();
    foreach ($this->updates as $key => $value) {
      if (is_string($value))
        $update[] = $this->maskstr($key).'='.$Controller->dbescape($value);
      else if (is_integer($value) || is_float($value))
        $update[] = $this->maskstr($key).'='.$value;
      else if (is_bool($value))
        $update[] = $this->maskstr($key).'='.intval($value);
      else if (is_null($value))
        $update[] = $this->maskstr($key).'=NULL';
      else
        throw new \Exception('NYI.');
    }
    $tbl = $this->maskstr($this->primaryTable);
    return 'UPDATE '.$tbl.' SET '.join(', ', $update).' WHERE '.join(' ', $this->conditions);
  }

  public function columns(array $cols) : QueryBuilder {
    $this->columns = array_unique(array_merge($this->columns, $cols));
    return $this;
  }

  private function maskstr(string $str, string $alias = null) : string {
    return '`'.$str.'`'.(!is_null($alias) ? ' `'.$alias.'`' : '');
  }

  private function maskField(string $table, string $alias, string $field, string $fieldalias = '', int $aggregation = 0) : string {
    if ($aggregation == 0)
      return $this->maskstr($alias).'.'.($field == DB_ANY ? $field : $this->maskstr($field));
    $field = ($field != DB_ANY ? $this->maskstr($alias).'.' : '' ).($field == DB_ANY ? $field : $this->maskstr($field));
    if (Flags::has_flag($aggregation, EAggregationType::atCOUNT) && Flags::has_flag($aggregation, EAggregationType::atDISTINCT))
      return 'COUNT(DISTINCT('.$field.'))'.($fieldalias != '' ? ' '.$this->maskstr($fieldalias) : '');
    if (Flags::has_flag($aggregation, EAggregationType::atCOUNT))
      return 'COUNT('.$field.')'.($fieldalias != '' ? ' '.$this->maskstr($fieldalias) : '');
    if (Flags::has_flag($aggregation, EAggregationType::atDISTINCT))
      return 'DISTINCT('.$field.')'.($fieldalias != '' ? ' '.$this->maskstr($fieldalias) : '');
    throw new \Exception('Invalid aggrgation value.');
  }

  private function maskTablefield(string $table, string $field) : string {
    return '`'.$table.'`.`'.$field.'`';
  }

  private function getTableAlias(string $table) : string {
    if (!array_key_exists($table, $this->aliases)) {
      $alias = '';
      for ($i = 1; $i < strlen($table); $i++) {
        $str = substr($table, 0, $i);
        if (!in_array($str, $this->aliases)) {
          $alias = $str;
          break;
        }
        if ($i > 1) {
          for ($j = 1; $j < 10; $j++) {
            $str2 = $str.$j;
            if (!in_array($str2, $this->aliases)) {
              $alias = $str2;
              break;
            }
          }
          if ($alias != '')
            break;
        }
      }
      $this->aliases[$table] = $alias;
      return $alias;
    }
    return $this->aliases[$table];
  }

  public function groupBy(...$params) : QueryBuilder {
    if (count($params) == 1) {
      if (!is_array($params[0]))
        $this->group($this->primaryTable, [$params[0]]);
      else
        $this->group($this->primaryTable, $params[0]);
    } else if (count($params) == 2) {
      if (!is_array($params[1]))
        $this->group($params[0], [$params[1]]);
      else
        $this->group($params[0], $params[1]);
    } else
      throw new \Exception('Invalid parameter count.');
    return $this;
  }

  private function group(string $table, array $group) : void {
    $alias = $this->getTableAlias($table);
    for ($i=0; $i<count($group); $i++)
      $this->groups[] = $this->maskTablefield($alias, $group[$i]);
  }

  public function join(string $table, ...$params) : QueryBuilder {
    $alias = $this->getTableAlias($table);
    $expr = 'JOIN '.$this->maskstr($table, $alias).' ';
    $expr .= 'ON ';
    $expr .= $this->maskTablefield($this->getTableAlias($params[0][0]), $params[0][1]);
    $expr .= ' '.$params[0][2].' ';
    if ($params[0][2] == 'IN')
      $expr .= '('.$params[0][3].')';
    else if (count($params[0]) == 4)
      $expr .= $params[0][3];
    else if (count($params[0]) == 5)
      $expr .= $this->maskTablefield($this->getTableAlias($params[0][3]), $params[0][4]);

    for ($i=1; $i<count($params); $i++) {
      if ($params[$i][0] != 'AND' && $params[$i][0] != 'OR')
        throw new \Exception('Invalid join definition.');
      $expr .= ' '.($i > 0 ? $params[$i][0].' ' : 'ON ');
      $expr .= $this->maskTablefield($this->getTableAlias($params[$i][1]), $params[$i][2]);
      $expr .= $params[$i][3];
      if ($params[$i][3] == 'IN')
        $expr .= '('.$params[$i][4].')';
      else if (count($params[$i]) == 5)
        $expr .= $params[$i][4];
      else if (count($params[$i]) == 6)
        $expr .= $this->maskTablefield($this->getTableAlias($params[$i][4]), $params[$i][5]);

    }
    $this->joinedTables[] = $expr;
    return $this;
  }

  public function joinLeft(string $table, ...$params) : QueryBuilder {
    $alias = $this->getTableAlias($table);
    $expr = 'LEFT JOIN '.$this->maskstr($table, $alias).' ';
    $expr .= 'ON ';
    $expr .= $this->maskTablefield($this->getTableAlias($params[0][0]), $params[0][1]);
    $expr .= ' '.$params[0][2].' ';
    if ($params[0][2] == 'IN')
      $expr .= '('.$params[0][3].')';
    else if (count($params[0]) == 4)
      $expr .= $params[0][3];
    else if (count($params[0]) == 5)
      $expr .= $this->maskTablefield($this->getTableAlias($params[0][3]), $params[0][4]);

    for ($i=1; $i<count($params); $i++) {
      if ($params[$i][0] != 'AND' && $params[$i][0] != 'OR')
        throw new \Exception('Invalid join definition.');
      $expr .= ' '.($i > 0 ? $params[$i][0].' ' : 'ON ');
      $expr .= $this->maskTablefield($this->getTableAlias($params[$i][1]), $params[$i][2]);
      $expr .= $params[$i][3];
      if ($params[$i][3] == 'IN')
        $expr .= '('.$params[$i][4].')';
      else if (count($params[$i]) == 5)
        $expr .= $params[$i][4];
      else if (count($params[$i]) == 6)
        $expr .= $this->maskTablefield($this->getTableAlias($params[$i][4]), $params[$i][5]);

    }
    $this->joinedTables[] = $expr;
    return $this;
  }

  public function limit(...$params) : QueryBuilder {
    if (count($params) == 1)
      $this->limitlen = intval($params[0]);
    else if (count($params) == 2) {
      $this->limitstart = intval($params[0]);
      $this->limitlen = intval($params[1]);
    } else
      throw new \Exception('Invalid parameter count.');
    return $this;
  }

  public function orderBy(...$params) : QueryBuilder {
    if (count($params) == 1) {
      if (!is_array($params[0]))
        $this->order($this->primaryTable, [$params[0]]);
      else
        $this->order($this->primaryTable, $params[0]);
    } else if (count($params) == 2) {
      if (!is_array($params[1]))
        $this->order($params[0], [$params[1]]);
      else
        $this->order($params[0], $params[1]);
    } else
      throw new \Exception('Invalid parameter count.');
    return $this;
  }

  private function order(string $table, array $order) : void {
    $alias = $this->getTableAlias($table);
    for ($i=0; $i<count($order); $i++) {
      if (!is_array($order[$i]))
        $this->orders[] = $this->maskTablefield($alias, $order[$i]);
      else
        $this->orders[] = $this->maskTablefield($alias, $order[$i][0]).' '.$order[$i][1];
    }
  }

  public function orderBy2(?string $table, string $column, string $direction) : void {
    if (!is_null($table)) {
      $alias = $this->getTableAlias($table);
      $this->orders[] = $this->maskTablefield($alias, $column.' '.$direction);
    } else
      $this->orders[] = $this->maskstr($column).' '.$direction;
  }

  public function select(...$params) : QueryBuilder {
    if (count($params) == 1) {
      if (!is_array($params[0]))
        $this->selectFields($this->primaryTable, [$params[0]]);
      else
        $this->selectFields($this->primaryTable, $params[0]);
    } else if (count($params) == 2) {
      if (!is_array($params[1]))
        $this->selectFields($params[0], [$params[1]]);
      else
        $this->selectFields($params[0], $params[1]);
    } else
      throw new \Exception('Invalid parameter count.');
    return $this;
  }

  private function selectFields(string $table, array $selector) : void {
    if (!array_key_exists($table, $this->selectors))
      $this->selectors[$table] = array();
    $alias = $this->getTableAlias($table);
    for ($i=0; $i<count($selector); $i++) {
      if (!is_array($selector[$i]))
        $selector[$i] = $this->maskField($table, $alias, $selector[$i]);
      else if (count($selector[$i]) == 2)
        $selector[$i] = $this->maskField($table, $alias, $selector[$i][0], '', $selector[$i][1]);
      else if (count($selector[$i]) == 3)
        $selector[$i] = $this->maskField($table, $alias, $selector[$i][0], $selector[$i][2], $selector[$i][1]);
      else
        throw new \Exception('Invalid parameter count.');
    }
    if (!array_key_exists($table, $this->selectors))
      $this->selectors[$table] = $selector;
    else
      $this->selectors[$table] = array_unique(array_merge($this->selectors[$table], $selector));
  }

  public function table(string $table) : QueryBuilder {
    $alias = $this->getTableAlias($table);
    if (is_null($this->primaryTable))
      $this->primaryTable = $table;
    else
      $this->joinedTables[$table] = array();
    return $this;
  }

  public function update(...$params) : QueryBuilder {
    if (is_array($params) && count($params) == 1) {
      if (is_array($params[0])) {
        foreach ($params[0] as $key => $value) {
          $this->updates[$key] = $value;
        }
        return $this;
      }
    }
    if (count($params) == 2)
      $this->updates[$params[0]] = $params[1];
    else
      throw new \Exception('Invalid parameter count.');
    return $this;
  }

  public function values(array $record) : QueryBuilder {
    $this->values[] = $record;
    return $this;
  }

  public function where(string $table, string $field, string $operator, $value = null) : QueryBuilder {
    global $Controller;
    if ($this->queryType == EQueryType::qtSELECT)
      $expr = $this->maskTablefield($this->getTableAlias($table), $field).' ';
    else
      $expr = $this->maskstr($field).' ';
    if ($operator == 'IS NULL' && is_null($value))
      $expr .= $operator;
    else {
      $expr .= $operator.' ';
      $expr .= $Controller->dbescape($value);
    }
    $this->conditions[] = $expr;
    return $this;
  }

  public function andWhere(string $table, string $field, string $operator, $value = null) : QueryBuilder {
    global $Controller;
    $expr = 'AND ';
    if ($this->queryType == EQueryType::qtSELECT)
      $expr .= $this->maskTablefield($this->getTableAlias($table), $field).' ';
    else
      $expr .= $this->maskstr($field).' ';
    if ($operator == 'IS NULL' && is_null($value))
      $expr .= $operator;
    else {
      $expr .= $operator.' ';
      $expr .= $Controller->dbescape($value);
    }
    $this->conditions[] = $expr;
    return $this;
  }

  public function orWhere(string $table, string $field, string $operator, $value = null) : QueryBuilder {
    global $Controller;
    $expr = 'OR ';
    if ($this->queryType == EQueryType::qtSELECT)
      $expr .= $this->maskTablefield($this->getTableAlias($table), $field).' ';
    else
      $expr .= $this->maskstr($field).' ';
    if ($operator == 'IS NULL' && is_null($value))
      $expr .= $operator;
    else {
      $expr .= $operator.' ';
      $expr .= $Controller->dbescape($value);
    }
    $this->conditions[] = $expr;
    return $this;
  }

}
