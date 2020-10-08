<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use \DateTime;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\EActivityType;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class RecipePostRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $recipe = $Controller->Dispatcher()->getObject();
    $payload = $Controller->Dispatcher()->getPayload();
    $user = $Controller->User();
    if (array_key_exists('publish', $payload))
      return self::publish($response, $recipe, $user);
    if (array_key_exists('unpublish', $payload))
      return self::unpublish($response, $recipe, $user);
    if (array_key_exists('vote', $payload))
      return self::vote($response, $recipe, $user, $payload['vote']);

    $response = $Controller->Config()->getResponseArray(71);

    return true;

  }

  static function publish(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    $recipe->setPublic(true);
    $response = $Controller->Config()->getResponseArray(1);
    parent::addToDictionary($response, ['page' => [ 'currentRecipe' => [ 'published' => $recipe->getPublishedDate()->format(DateTime::ISO8601) ]]]);
    return true;
  }

  static function unpublish(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    $recipe->setPublic(false);
    $response = $Controller->Config()->getResponseArray(1);
    parent::addToDictionary($response, ['page' => [ 'currentRecipe' => [ 'published' => false ]]]);
    return true;
  }

  static function vote(array &$response, RecipeInterface $recipe, ?UserInterface $user, array $voting) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() == $user->getId() || !$recipe->isPublished()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    $cooked = intval($voting['cooked']);
    $rated = intval($voting['rating']);
    $voted = intval($voting['voting']);
    if (($cooked < -1 && $cooked > 1) ||
      ($rated < -1 && $rated > 3) ||
      ($voted < 1 && $voted > 5) ||
      ($cooked == -1 && $rated == -1 && $voted == -1)) {
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
      $id = $Controller->getInsertId();
      $Controller->addActivity(
        EActivityType::RatingAdded, [
          'rating_id' => $id,
        ], $recipe, null, $id);
      $response = $Controller->Config()->getResponseArray(1);
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    return false;
  }

}
