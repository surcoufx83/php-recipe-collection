<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Pictures\DummyPicture;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class RecipeListRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $filter = $Controller->Dispatcher()->getFromMatches('filter');
    $response = $Controller->Config()->getResponseArray(1);

    $home = $Controller->Dispatcher()->getFromMatches('home');
    if ($home == 'home')
      $filter = 'home';

    $countQuery = new QueryBuilder(EQueryType::qtSELECT, 'recipes');
    $countQuery
      ->select2('recipes', 'recipe_id', ['alias' => 'count', 'aggregation' => EAggregationType::atCOUNT]);

    $baseQuery = new QueryBuilder(EQueryType::qtSELECT, 'recipes');
    $baseQuery
      ->select('recipes', ['recipe_id', 'user_id', 'recipe_public', 'recipe_name', 'recipe_description', 'recipe_eater', 'recipe_source_desc', 'recipe_source_url', 'recipe_created', 'recipe_published'])
      ->select('recipe_pictures', ['picture_id', 'picture_sortindex', 'picture_name', 'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'])
      ->select2('recipe_ratings', 'entry_viewed', ['alias' => 'views', 'aggregation' => EAggregationType::atSUM])
      ->select2('recipe_ratings', 'entry_cooked', ['alias' => 'cooked', 'aggregation' => EAggregationType::atSUM])
      ->select2('recipe_ratings', 'entry_vote', ['alias' => 'votesum', 'aggregation' => EAggregationType::atSUM])
      ->select2('recipe_ratings', 'entry_vote', ['alias' => 'votes', 'aggregation' => EAggregationType::atCOUNT])
      ->select2('recipe_ratings', 'entry_comment', ['alias' => 'comments', 'aggregation' => EAggregationType::atCOUNT])
      ->joinLeft('recipe_pictures',
          ['recipe_pictures', 'recipe_id', '=', 'recipes', 'recipe_id'],
          ['AND', 'recipe_pictures', 'picture_sortindex', '=', 0]
        )
      ->joinLeft('recipe_ratings', ['recipe_ratings', 'recipe_id', '=', 'recipes', 'recipe_id'])
      ->groupBy('recipes', ['recipe_id', 'user_id', 'recipe_public', 'recipe_name', 'recipe_description', 'recipe_eater', 'recipe_source_desc', 'recipe_source_url', 'recipe_created', 'recipe_published'])
      ->groupBy('recipe_pictures', ['picture_id', 'picture_sortindex', 'picture_name', 'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'])
      ->limit($Controller->Config()->Defaults('Lists', 'Entries'));

    if (is_null($filter) || $filter == '')
      self::unfilteredList($response, $baseQuery, $countQuery);
    else if ($filter == 'home')
      self::filterHomeList($response, $baseQuery, $countQuery);
    else if ($filter == 'my')
      self::filterOwnList($response, $baseQuery, $countQuery);
    else if ($filter == 'user') {
      $user = $Controller->OM()->User(intval($Controller->Dispatcher()->getFromMatches('id')));
      if (is_null($user)) {
        $response = $Controller->Config()->getResponseArray(80);
        return false;
      }
      self::filterUserList($response, $baseQuery, $countQuery, $user);
    }

    if ($filter !== 'home') {
      $baseQuery->orderBy2('recipes', 'recipe_name', 'ASC');
    }

    $count = $Controller->select($countQuery)->fetch_assoc()['count'];
    $result = $Controller->select($baseQuery);
    if (!$result) {
      $response = $Controller->Config()->getResponseArray(204);
      return false;
    }

    $data = [
      'count' => $count,
      'page' => 0,
      'pages' => ceil($count / $Controller->Config()->Defaults('Lists', 'Entries')),
      'itemsPerPage' => $Controller->Config()->Defaults('Lists', 'Entries'),
      'records' => []
    ];

    while ($record = $result->fetch_array()) {
      $recipe = new Recipe($record);
      if (!is_null($record['picture_id']))
        $picture = new Picture($record);
      else
        $picture = new DummyPicture(0);
      $recipe->addPicture($picture);
      $data['records'][] = [
        'recipe' => $recipe,
        'views' => intval($record['views']),
        'cooked' => intval($record['cooked']),
        'votes' => intval($record['votes']),
        'voting' => (intval($record['votes']) > 0 ? Formatter::float_format(doubleval($record['votesum']) / doubleval($record['votes']), 1) : 0),
        'comments' => intval($record['comments']),
        'showAuthor' => ($filter != 'my'),
      ];
    }

    parent::addToDictionary($response, ['page' => [ 'customContent' => $data ]]);
    return true;
  }

  static function filterHomeList(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery) : void {
    global $Controller;
    $basequery->orderBy2('recipes', 'recipe_published', 'DESC');
  }

  static function filterOwnList(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery) : void {
    global $Controller;
    $basequery->where('recipes', 'user_id', '=', $Controller->User()->getId());
    $countquery->where('recipes', 'user_id', '=', $Controller->User()->getId());
  }

  static function filterUserList(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery, UserInterface $user) : void {
    global $Controller;
    $basequery
      ->where('recipes', 'recipe_public', '=', 1)
      ->andWhere('recipes', 'user_id', '=', $user->getId());
    $countquery
      ->where('recipes', 'recipe_public', '=', 1)
      ->andWhere('recipes', 'user_id', '=', $user->getId());
  }

  static function unfilteredList(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery) : void {
    global $Controller;
    $basequery->where('recipes', 'recipe_public', '=', 1);
    $countquery->where('recipes', 'recipe_public', '=', 1);
  }

}
