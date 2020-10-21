<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use \DateTime;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Helper\Formatter;

if (!defined('CORE2'))
  exit;

class RecipePageRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $recipe = $Controller->Dispatcher()->getObject();
    if (!$recipe->isPublished() && $recipe->getUserId() != $Controller->User()->getId()) {
      $response = $Controller->Config()->getResponseArray(3);
      return true;
    }

    $recipe->loadComplete();

    if ($recipe->getUserId() != $Controller->User()->getId())
      self::addViewer($recipe);

    $userVote = self::getMyVote($recipe);

    if (!is_null($recipe->getUserId())) {
      parent::setTitle($response,  $Controller->l('recipe_about_title_withUser', $recipe->getName(), $recipe->getUser()->getUsername()));
    } else {
      parent::setTitle($response,  $Controller->l('recipe_about_title_noUser', $recipe->getName()));
    }

    parent::setDescription($response, $recipe->getDescription());

    parent::addToDictionary($response, ['page' => [ 'contentData' => [ 'hasActions' => true ]]]);
    parent::addToDictionary($response, ['page' => [ 'currentRecipe' => $recipe ]]);
    parent::addToDictionary($response, ['page' => [ 'self' => [
      'hasVoted' => (!is_null($userVote)) ? true : false,
      'lastVote' => $userVote,
      'voteCount' => self::getMyVoteCount($recipe),
      'visitCount' => self::getMyVisitCount($recipe),
    ]]]);

    return true;

  }

  private static function addViewer(RecipeInterface $recipe) : void {
    global $Controller;
    $maxage = (new DateTime())->sub($Controller->Config()->RecipeVisitedClearance());
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
          ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_viewed', '=', '1');
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
    $Controller->insertSimple(
      'recipe_ratings',
      ['user_id', 'recipe_id', 'entry_viewed'],
      [$Controller->User()->getId(), $recipe->getId(), 1]
    );
  }

  private static function getMyVote(RecipeInterface $recipe) : ?RatingInterface {
    global $Controller;
    $maxage = (new DateTime())->sub($Controller->Config()->RecipeRatingClearance());
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
          ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_comment', 'IS NULL')
          ->andWhere('recipe_ratings', 'entry_viewed', 'IS NULL')
          ->orderBy2('recipe_ratings', 'entry_datetime', 'DESC')
          ->limit(1);
    $result = $Controller->select($query);
    if ($result && $result->num_rows > 0)
      return $Controller->OM()->Rating($result->fetch_assoc());
    return null;
  }

  private static function getMyVoteCount(RecipeInterface $recipe) : int {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings');
    $query->select([['*', EAggregationType::atCOUNT, 'count']])
          ->where('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_viewed', 'IS NULL');
    return $Controller->select($query)->fetch_assoc()['count'];
  }

  private static function getMyVisitCount(RecipeInterface $recipe) : int {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings');
    $query->select([['*', EAggregationType::atCOUNT, 'count']])
          ->where('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_viewed', '=', 1);
    return $Controller->select($query)->fetch_assoc()['count'];
  }

}
