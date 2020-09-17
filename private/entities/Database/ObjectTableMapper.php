<?php

namespace Surcouf\Cookbook\Database;

use Surcouf\Cookbook\Database\Mapper\TableMapperInterface;
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
      case CookingStep::class:
        return new Mapper\StepMapper();
      case Ingredient::class:
        return new Mapper\IngredientMapper();
      case Picture::class:
        return new Mapper\PictureMapper();
      case Rating::class:
        return new Mapper\RatingMapper();
      case Recipe::class:
        return new Mapper\RecipeMapper();
      case Tag::class:
        return new Mapper\TagMapper();
      case Unit::class:
        return new Mapper\UnitMapper();
      case User::class:
        return new Mapper\UserMapper();
      default:
        throw new \Exception('Mapper \''.$className.'\' is not implemented.');
    }
  }

}
