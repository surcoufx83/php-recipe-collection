<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Document\Category;
use Surcouf\Cookbook\Document\Type;
use Surcouf\Cookbook\File\Extension;

if (!defined('CORE2'))
  exit;

interface IController {

  public function Config() : Config;
  public function Dispatcher() : Dispatcher;
  public function User() : ?User;

  public function cancelTransaction() : bool;
  public function dberror() : string;
  public function dbescape($value, bool $includeQuotes = true) : string;
  public function delete(QueryBuilder &$qbuilder) : bool;
  public function finishTransaction() : bool;
  public function get(array $params) : void;
  public function getInsertId() : ?int;
  public function getLink(string $filter, ...$args) : ?string;
  public function getPicture($filter) : ?Picture;
  public function getRating($filter) : ?Rating;
  public function getRecipe($filter) : ?Recipe;
  public function getStep($filter) : ?CookingStep;
  public function getTag($filter) : ?Tag;
  public function getUnit($filter) : ?Unit;
  public function getUser($filter=null) : ?User;
  public function init() : void;
  public function insert(QueryBuilder &$qbuilder) : bool;
  public function insertSimple(string $table, array $columns, array $data) : int;
  public function isAuthenticated() : bool;
  public function l(string $key, ...$params) : string;
  public function loadRecipeIngredients(Recipe &$recipe) : void;
  public function loadRecipePictures(Recipe &$recipe) : void;
  public function loadRecipeRatings(Recipe &$recipe) : void;
  public function loadRecipeSteps(Recipe &$recipe) : void;
  public function loginWithPassword(string $email, string $password, bool $keepSession, array &$response = null) : bool;
  public function logout() : void;
  public function on(string $method, array $params) : void;
  public function post(array $params) : void;
  public function put(array $params) : void;
  public function select(QueryBuilder &$qbuilder) : ?\mysqli_result;
  public function selectCountSimple(string $table, string $filterColumn=null, string $filterValue=null) : int;
  public function setSessionCookies(string $userCookie, string $tokenCookie, string $passwordCookie, bool $longDuration) : bool;
  public function startTransaction() : bool;
  public function tearDown() : void;
  public function update(QueryBuilder &$qbuilder) : bool;
  public function updateDbObject(IDbObject &$object) : void;

}
