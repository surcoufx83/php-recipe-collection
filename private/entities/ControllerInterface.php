<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Controller\Dispatcher;
use Surcouf\Cookbook\Controller\ObjectManager;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

interface ControllerInterface {

  public function Config() : ConfigInterface;
  public function Dispatcher() : Dispatcher;
  public function ObjectManager() : ObjectManager;
  public function OM() : ObjectManager;
  public function User() : ?UserInterface;

  public function cancelTransaction() : bool;
  public function dberror() : string;
  public function dbescape($value, bool $includeQuotes = true) : string;
  public function delete(QueryBuilder &$qbuilder) : bool;
  public function finishTransaction() : bool;
  public function get(array $params) : void;
  public function getInsertId() : ?int;
  public function getLink(string $filter, ...$args) : ?string;
  public function init() : void;
  public function insert(QueryBuilder &$qbuilder) : bool;
  public function insertSimple(string $table, array $columns, array $data) : int;
  public function isAuthenticated() : bool;
  public function l(string $key, ...$params) : string;
  public function loginWithPassword(string $email, string $password, bool $keepSession, array &$response = null) : bool;
  public function logout() : void;
  public function on(string $method, array $params) : void;
  public function post(array $params) : void;
  public function put(array $params) : void;
  public function select(QueryBuilder &$queryBuilder) : ?\mysqli_result;
  public function selectCountSimple(string $table, string $filterColumn=null, string $filterValue=null) : int;
  public function selectFirst(QueryBuilder &$queryBuilder);
  public function setSessionCookies(string $userCookie, string $tokenCookie, string $passwordCookie, bool $longDuration) : bool;
  public function startTransaction() : bool;
  public function tearDown() : void;
  public function update(QueryBuilder &$qbuilder) : bool;
  public function updateDbObject(DbObjectInterface &$object) : void;

}
