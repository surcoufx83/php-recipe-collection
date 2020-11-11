<?php

namespace Surcouf\Cookbook\Database;

use Surcouf\Cookbook\Helper\Flags;
use Surcouf\Cookbook\Database\Builder\WhereCondition;

if (!defined('CORE2'))
  exit;

define('DB_ANY', '*');
$dbTableAliase = array();

final class QueryBuilder {

  private $queryType;

  private $primaryTable;
  private $selectors = array();
  private $updates = array();
  private $joinedTables = array();
  private $conditions = array();
  private $where;
  private $groups = array();
  private $orders = array();
  private $limitstart = -1;
  private $limitlen = -1;
  private $columns = array();
  private $values = array();

  public function __construct(int $queryType = EQueryType::None, string $table = null, $selector = null) {
    $this->queryType = $queryType;
    if (!is_null($table))
      $this->table($table);
    if (!is_null($selector) && $queryType == EQueryType::qtSELECT)
      $this->select($table, $selector);
    else if (!is_null($selector) && $queryType == EQueryType::qtINSERT)
      $this->columns($selector);
  }

  public function __toString() : string {
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

  public function buildQuery() : string {
    return $this->__toString();
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
      $alias = $this->getTableAlias($key);
      for ($i=0; $i<count($value); $i++) {
        $sel[] = $value[$i];
      }
    }
    $tbl = $this->maskstr($this->primaryTable, $this->getTableAlias($this->primaryTable));
    $joinsexpr = (count($this->joinedTables) != 0 ? ' '.join(' ', $this->joinedTables) : '');
    if (!is_null($this->where))
      $condexpr = ' WHERE '.$this->where;
    else
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

  private static function maskstr(string $str, string $alias = null) : string {
    return '`'.$str.'`'.(!is_null($alias) ? ' `'.$alias.'`' : '');
  }

  public static function maskField(string $table, string $alias, string $field, string $fieldalias = '', int $aggregation = 0, $fnparam1 = null, $fnparam2 = null, $fnparam3 = null) : string {
    $maskedfield = ($aggregation == 0 ? self::maskstr($alias).'.' : '').($field != DB_ANY ? self::maskstr($field) : DB_ANY);

    if (Flags::has_flag($aggregation, EAggregationType::atISNULL))
      $maskedfield = self::maskedField(EAggregationType::atISNULL, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atISNOTNULL))
      $maskedfield = self::maskedField(EAggregationType::atISNOTNULL, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atDISTINCT))
      $maskedfield = self::maskedField(EAggregationType::atDISTINCT, $maskedfield);

    if (Flags::has_flag($aggregation, EAggregationType::atAVG))
      $maskedfield = self::maskedField(EAggregationType::atAVG, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atCONCAT))
      $maskedfield = self::maskedField(EAggregationType::atCONCAT, $maskedfield, $fnparam1);
    if (Flags::has_flag($aggregation, EAggregationType::atCOUNT))
      $maskedfield = self::maskedField(EAggregationType::atCOUNT, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atGRPCONCAT))
      $maskedfield = self::maskedField(EAggregationType::atGRPCONCAT, $maskedfield, $fnparam1);
    if (Flags::has_flag($aggregation, EAggregationType::atMAX))
      $maskedfield = self::maskedField(EAggregationType::atMAX, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atMIN))
      $maskedfield = self::maskedField(EAggregationType::atMIN, $maskedfield);
    if (Flags::has_flag($aggregation, EAggregationType::atSUM))
      $maskedfield = self::maskedField(EAggregationType::atSUM, $maskedfield);

    if (Flags::has_flag($aggregation, EAggregationType::atIFNULL))
      $maskedfield = self::maskedField(EAggregationType::atIFNULL, $maskedfield, $fnparam1);

    if ($fieldalias != '')
      $maskedfield .= ' '.self::maskstr($fieldalias);

    return $maskedfield;

  }

  private static function maskedField(int $aggregation, string $makedfield, $param = null) : string {
    switch($aggregation) {
      case EAggregationType::atAVG:
        return 'AVG('.$makedfield.')';
      case EAggregationType::atCONCAT:
        if (!is_null($param))
          return 'CONCAT_WS(\''.$param.'\', '.$makedfield.')';
        return 'CONCAT('.$makedfield.')';
      case EAggregationType::atCOUNT:
        return 'COUNT('.$makedfield.')';
      case EAggregationType::atDISTINCT:
        return 'DISTINCT('.$makedfield.')';
      case EAggregationType::atGRPCONCAT:
        return 'GROUP_CONCAT('.$makedfield.(!is_null($param) ? ' SEPARATOR \''.$param.'\'' : '').')';
      case EAggregationType::atISNULL:
        return 'IS NULL '.$makedfield;
      case EAggregationType::atISNOTNULL:
        return 'IS NOT NULL '.$makedfield;
      case EAggregationType::atMAX:
        return 'MAX('.$makedfield.')';
      case EAggregationType::atMIN:
        return 'MIN('.$makedfield.')';
      case EAggregationType::atSUM:
        return 'SUM('.$makedfield.')';
      case EAggregationType::atIFNULL:
        return 'IFNULL('.$makedfield.', '.$param.')';
    }
  }

  private function maskTablefield(string $table, string $field) : string {
    return '`'.$table.'`.`'.$field.'`';
  }

  public static function getTableAlias(string $table) : string {
    global $dbTableAliase;
    if (!is_array($dbTableAliase))
      $dbTableAliase = array();
    if (!array_key_exists($table, $dbTableAliase)) {
      $alias = '';
      for ($i = 1; $i < strlen($table); $i++) {
        $str = substr($table, 0, $i);
        if (!in_array($str, $dbTableAliase)) {
          $alias = $str;
          break;
        }
        if ($i > 1) {
          for ($j = 1; $j < 10; $j++) {
            $str2 = $str.$j;
            if (!in_array($str2, $dbTableAliase)) {
              $alias = $str2;
              break;
            }
          }
          if ($alias != '')
            break;
        }
      }
      $dbTableAliase[$table] = $alias;
      return $alias;
    }
    return $dbTableAliase[$table];
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

  public function orderBy2(?string $table, string $column, string $direction) : QueryBuilder {
    if (!is_null($table)) {
      $alias = $this->getTableAlias($table);
      $this->orders[] = $this->maskTablefield($alias, $column).' '.$direction;
    } else
      $this->orders[] = $this->maskstr($column).' '.$direction;
    return $this;
  }

  public function orderRandom() : QueryBuilder {
    $this->orders[] = 'RAND()';
    return $this;
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

  public function select2(string $table, string $field, array $params) : QueryBuilder {
    $alias = $this->getTableAlias($table);
    if (!array_key_exists($table, $this->selectors))
      $this->selectors[$table] = array();
    $fieldalias = array_key_exists('alias', $params) ? $params['alias'] : '';
    $aggregation = array_key_exists('aggregation', $params) ? $params['aggregation'] : 0;
    $fnparam1 = array_key_exists('param1', $params) ? $params['param1'] : null;
    $fnparam2 = array_key_exists('param2', $params) ? $params['param2'] : null;
    $fnparam3 = array_key_exists('param3', $params) ? $params['param3'] : null;
    $this->selectors[$table][] = $this->maskField($table, $alias, $field, $fieldalias, $aggregation, $fnparam1, $fnparam2, $fnparam3);
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

  public function setTable(string $table) : QueryBuilder {
    $alias = $this->getTableAlias($table);
    if (is_null($this->primaryTable))
      $this->primaryTable = $table;
    else
      $this->joinedTables[$table] = array();
    return $this;
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
    if (is_array($record[0])) {
      for ($i=0; $i<count($record); $i++) {
        $this->values[] = $record[$i];
      }
    }
    else
      $this->values[] = $record;
    return $this;
  }

  public function setWhere() : WhereCondition {
    if (is_null($this->where))
      $this->where = new WhereCondition($this);
    return $this->where;
  }

  public function where(string $table, string $field, string $operator, $value = null) : QueryBuilder {
    global $Controller;
    if ($this->queryType == EQueryType::qtSELECT)
      $expr = $this->maskTablefield($this->getTableAlias($table), $field).' ';
    else
      $expr = $this->maskstr($field).' ';
    if ($operator == 'IS NULL' && is_null($value))
      $expr .= $operator;
    else if ($operator == 'IN' && is_array($value)) {
      for ($i=0; $i<count($value); $i++) {
        $value[$i] = $Controller->dbescape($value[$i]);
      }
      $expr .= $operator.' ('.implode(', ', $value).')';
    }
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
    else if ($operator == 'IN' && is_array($value)) {
      for ($i=0; $i<count($value); $i++) {
        $value[$i] = $Controller->dbescape($value[$i]);
      }
      $expr .= $operator.' ('.implode(', ', $value).')';
    }
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
    else if ($operator == 'IN' && is_array($value)) {
      for ($i=0; $i<count($value); $i++) {
        $value[$i] = $Controller->dbescape($value[$i]);
      }
      $expr .= $operator.' ('.implode(', ', $value).')';
    }
    else {
      $expr .= $operator.' ';
      $expr .= $Controller->dbescape($value);
    }
    $this->conditions[] = $expr;
    return $this;
  }

}
