<?php

namespace Surcouf\Cookbook\Config;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Database\QueryBuilder;

if (!defined('CORE2'))
  exit;

interface DatabaseManagerInterface {

  public function cancelTransaction() : bool;
  public function dberror() : string;
  public function dbescape($value, string $separator = ', ') : string;
  public function delete(QueryBuilder &$qbuilder) : bool;
  public function finishTransaction() : bool;
  public function getInsertId() : ?int;
  public function insert(QueryBuilder &$qbuilder) : bool;
  public function insertSimple(string $table, array $columns, array $data) : int;
  public function select(QueryBuilder &$queryBuilder) : ?\mysqli_result;
  public function selectCountSimple(string $table, string $filterColumn=null, string $filterValue=null) : int;
  public function selectFirst(QueryBuilder &$queryBuilder);
  public function startTransaction() : bool;
  public function update(QueryBuilder &$qbuilder) : bool;
  public function updateDbObject(DbObjectInterface &$object) : void;

  public function setDatabaseDbName(string $dbname) : DatabaseManagerInterface;
  public function setDatabaseHost(string $hostname) : DatabaseManagerInterface;
  public function setDatabasePassword(string $password) : DatabaseManagerInterface;
  public function setDatabaseUser(string $username) : DatabaseManagerInterface;

}
