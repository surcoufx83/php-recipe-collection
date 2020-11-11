<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

if (!defined('CORE2'))
  exit;

class RandomRecipeRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes');
    $query->select('recipes', ['recipe_id', 'recipe_name'])
          ->where('recipes', 'recipe_public', '=', 1)
          ->orderRandom()
          ->limit(1);
    $result = $Controller->select($query);
    if (!$result || $result->num_rows == 0)
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
    $record = $result->fetch_assoc();
    $Controller->Dispatcher()->forwardTo($Controller->getLink('recipe:show', $record['recipe_id'], $record['recipe_name']));
  }

}
