<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Recipe\Recipe;

if (!defined('CORE2'))
  exit;

class RecipesRoute extends Route implements RouteInterface {

  private static $template = 'books/myrecipes';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    parent::addBreadcrumb($Controller->getLink('private:recipes'), $Controller->l('page_myrecipes_title'));
    $recipes = [];
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
    $query
      ->where('recipes', 'user_id', '=', $Controller->User()->getId())
      ->orderBy2('recipes', 'recipe_name', 'ASC');
    $result = $Controller->select($query);
    while ($record = $result->fetch_object(Recipe::class)) {
      $recipe = $Controller->OM()->Recipe($record);
      $recipe->loadRecipePictures($Controller);
      $recipe->loadRecipeRatings($Controller);
      $recipe->loadRecipeTags($Controller);
      $recipes[] = $recipe;
    }

    parent::addToDictionary('Recipes', $recipes);
    parent::setPage('private:recipes');
    parent::setTitle($Controller->l('page_myrecipes_title'));
    return parent::render(self::$template, $response);
  }

}
