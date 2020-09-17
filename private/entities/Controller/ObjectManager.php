<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\ObjectTableMapper;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Helper\DatabaseHelper;
use Surcouf\Cookbook\Recipe\Cooking\CookingStep;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Ingredient;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\Unit;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\Rating;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\Tag;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\User;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class ObjectManager {

  protected $cache = [];

  public function __call(string $name, array $arguments) : ?object {
    switch ($name) {

      case 'CookingStep':
      case 'Step':
        return $this->Object(CookingStep::class, $arguments[0]);

      case 'Ingredient':
        return $this->Object(Ingredient::class, $arguments[0]);

      case 'Picture':
        return $this->Object(Picture::class, $arguments[0]);

      case 'Rating':
        return $this->Object(Rating::class, $arguments[0]);

      case 'Recipe':
        return $this->Object(Recipe::class, $arguments[0]);

      case 'Tag':
        return $this->Object(Tag::class, $arguments[0]);

      case 'Unit':
        return $this->Object(Unit::class, $arguments[0]);

      case 'User':
        return $this->Object(User::class, $arguments[0]);

      default:
        throw new \Exception('Method \''.$name.'\' does not exist in ObjectManager.');
    }
  }

  private function Object(string $className, $filter = null) : ?object {
    $mapper = ObjectTableMapper::getMapper($className);
    if (is_integer($filter)) {
      $obj = $this->getObject($className, $filter);
      if (!is_null($obj))
        return $obj;
      return $this->getObjectFromDatabase($className, $filter);
    }
    if ($filter instanceOf DbObjectInterface)
      return $this->addObjectToCache($filter);
    if (is_a($filter, QueryBuilder::class))
      return $this->getObjectFromQueryBuilder($filter);
    if (is_array($filter))
      return $this->getObjectFromArray($className, $filter);
    if (is_string($filter) && $mapper->NameSearch() == true)
      return $this->getObjectFromDatabaseByName($className, $filter);
    throw new \Exception('First Parameter must be int or instance of DbObjectInterface or an array with the database record.');
  }

  private function addObjectToCache($object) : object {
    $className = get_class($object);
    if (!array_key_exists($className, $this->cache))
      $this->cache[$className] = [];
    $this->cache[$className][$object->getId()] = $object;
    return $object;
  }

  private function addPlaceholderToCache(string $className, int $id) : void {
    if (!array_key_exists($className, $this->cache))
      $this->cache[$className] = [];
    $this->cache[$className][$id] = null;
  }

  private function getObject(string $className, int $id) : ?object {
    if (array_key_exists($className, $this->cache)
     && array_key_exists($id, $this->cache[$className])) {
      return $this->cache[$className][$id];
    }
    return null;
  }

  private function getObjectFromArray(string $className, array $record) : ?object {
    global $Controller;
    $mapper = ObjectTableMapper::getMapper($className);
    if (array_key_exists($mapper->IdColumn(), $record)) {
      $object = new $className($record);
      return $object;
    }
    return null;
  }

  private function getObjectFromDatabase(string $className, int $id) : ?object {
    global $Controller;
    $mapper = ObjectTableMapper::getMapper($className);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query
      ->where($mapper->TableName(), $mapper->IdColumn(), '=', $id)
      ->limit(1);
    $object = $Controller->selectObject($query, $className);
    if (!is_null($object))
      $this->addObjectToCache($object);
    else
      $this->addPlaceholderToCache($className, $id);
    return $object;
  }

  private function getObjectFromDatabaseByName(string $className, string $name) : ?object {
    global $Controller;
    $mapper = ObjectTableMapper::getMapper($className);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query
      ->where($mapper->TableName(), $mapper->NameColumn(), '=', $name)
      ->limit(1);
    if ($className == User::class)
      $query->orWhere($mapper->TableName(), 'user_email', '=', $name);
    $object = $Controller->selectObject($query, $className);
    if (!is_null($object))
      $this->addObjectToCache($object);
    return $object;
  }

  private function getObjectFromQueryBuilder(string $className, QueryBuilder $queryBuilder) : ?object {
    global $Controller;
    $object = $Controller->selectObject($queryBuilder, $className);
    if (!is_null($object))
      $this->addObjectToCache($object);
    return $object;
  }

}
