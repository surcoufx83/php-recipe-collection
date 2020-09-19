<?php

namespace Surcouf\Cookbook\Controller\Routes\Recipe;

use \DateTime;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;

if (!defined('CORE2'))
  exit;

class RecipVoteRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $recipe = $Controller->Dispatcher()->getObject();
    $payload = $Controller->Dispatcher()->getPayload();

    if (is_null($recipe) ||
      $recipe->getUserId() == $Controller->User()->getId()) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $cooked = intval($payload['cooked']);
    $rated = intval($payload['rated']);
    $voted = intval($payload['voted']);

    if (($cooked < -1 && $cooked > 1) ||
      ($rated < -1 && $rated > 3) ||
      ($voted < 0 && $voted > 5) ||
      ($cooked == -1 && $rated == -1 && $voted == 0)) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $maxage = (new DateTime())->sub($Controller->Config()->RecipeRatingClearance());
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
          ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_comment', 'IS NULL')
          ->andWhere('recipe_ratings', 'entry_viewed', 'IS NULL');
    $result = $Controller->select($query);
    if ($result && $result->num_rows > 0) {
      $keys = [];
      while ($record = $result->fetch_assoc()) {
        $keys[] = intval($record['entry_id']);
      }
      $query = new QueryBuilder(EQueryType::qtDELETE, 'recipe_ratings');
      $query->where('recipe_ratings', 'entry_id', 'IN', $keys)
            ->limit(count($keys));
      $Controller->delete($query);
    }

    $result = $Controller->insertSimple(
      'recipe_ratings',
      ['user_id', 'recipe_id', 'entry_cooked', 'entry_vote', 'entry_rate'],
      [$Controller->User()->getId(),
       $recipe->getId(),
       $cooked > -1 ? $cooked : null,
       $voted > -1 ? $voted : null,
       $rated > -1 ? $rated : null]
    );

    if ($result != -1) {
      $response = $Controller->Config()->getResponseArray(1);
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    return false;

  }

}
