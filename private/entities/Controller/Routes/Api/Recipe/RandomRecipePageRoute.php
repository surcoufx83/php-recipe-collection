<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Recipe\Recipe;

if (!defined('CORE2'))
  exit;

class RandomRecipePageRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
    $query->where('recipes', 'recipe_public', '=', 1)
          ->orderRandom()
          ->limit(1);
    $result = $Controller->select($query);
    if ($result && $result->num_rows == 1) {
      $response = $Controller->Config()->getResponseArray(4);
      $recipe = $result->fetch_object(Recipe::class);
      parent::forwardResponse($response, 'recipe', ['id' => $recipe->getId(), 'name' => Formatter::nice_urlstring($recipe->getName())]);
      return true;
    }
    else
      $response = $Controller->Config()->getResponseArray(3);
    return true;
  }

}
