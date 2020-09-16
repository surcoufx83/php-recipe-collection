<?php

namespace Surcouf\Cookbook\Database;

use Surcouf\Cookbook\Database\Mapper\TableMapperInterface;
use Surcouf\Cookbook\Database\Mapper\RecipeMapper;
use Surcouf\Cookbook\Recipe\Cooking\CookingStep;
use Surcouf\Cookbook\Recipe\Ingredients\Ingredient;
use Surcouf\Cookbook\Recipe\Ingredients\Units\Unit;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Social\Ratings\Rating;
use Surcouf\Cookbook\Recipe\Social\Tags\Tag;
use Surcouf\Cookbook\User\User;

if (!defined('CORE2'))
  exit;

final class ObjectTableMapper {

  static function getMapper(string $className) : TableMapperInterface {
    switch($className) {
      case Recipe::class:
        return new RecipeMapper();
      default:
        throw new \Exception('Mapper is not implemented.');
    }
  }

}
