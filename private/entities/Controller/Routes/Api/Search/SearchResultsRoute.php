<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Search;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Database\Builder\Expression;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class SearchResultsRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $request = $Controller->Dispatcher()->getPayload();
    if (!\array_key_exists('search', $request) || !\is_array($request['search']) || !\array_key_exists('phrase', $request['search'])) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $response = $Controller->Config()->getResponseArray(1);
    $querystring = $request['search']['phrase'];
    $searchpage = !\array_key_exists('page', $request['search']) ? 0 : intval($request['search']['page']);
    $queryitems = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|"."[\s,]*'([^']+)'[\s,]*|"."[\s,]+/", $querystring, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    if (count($queryitems) == 0) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $countQuery = new QueryBuilder(EQueryType::qtSELECT, 'allrecipetextdata');
    $countQuery = $countQuery
      ->select2('allrecipetextdata', 'recipe_id', ['alias' => 'count', 'aggregation' => EAggregationType::atCOUNT])
      ->setWhere()
        ->expr();

    $baseQuery = new QueryBuilder(EQueryType::qtSELECT, 'allrecipes');
    $baseQuery = $baseQuery
      ->select(DB_ANY)
      ->select('users', ['user_name', 'oauth_user_name', 'user_firstname'])
      ->join('allrecipetextdata', ['allrecipetextdata', 'recipe_id', '=', 'allrecipes', 'recipe_id'])
      ->joinLeft('users', ['users', 'user_id', '=', 'allrecipes', 'user_id'])
      ->orderBy2('allrecipes', 'recipe_name', 'ASC')
      ->limit($searchpage * $Controller->Config()->DefaultListEntries(), $Controller->Config()->DefaultListEntries())
      ->setWhere()->expr();

    for ($i=0; $i<count($queryitems); $i++) {
      if ($i > 0) {
        $baseQuery->and();
        $countQuery->and();
      }
      $baseQuery
        ->e()
          ->braces()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_name'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_description'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_ingredients'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_steps'], 'right' => $queryitems[$i]], false);
      $countQuery
        ->e()
          ->braces()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_name'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_description'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_ingredients'], 'right' => $queryitems[$i]], true)
          ->or()
          ->contains(['left' => ['table' => 'allrecipetextdata', 'column' => 'recipe_steps'], 'right' => $queryitems[$i]], false);
    }
    $baseQuery = $baseQuery->ret()->ret();
    $countQuery = $countQuery->ret()->ret();
    $records = $Controller->selectFirst($countQuery)['count'];
    $response['page'] = [
      'search' => [
        'records' => [
          'total' => $records,
          'numpages' => ceil($records / $Controller->Config()->DefaultListEntries()),
          'page' => $searchpage
        ],
        'results' => []
      ]
    ];

    if ($searchpage > $response['page']['search']['records']['numpages'])
      return true;

    $result = $Controller->select($baseQuery);
    if (!is_null($result)) {
      while($record = $result->fetch_assoc()) {
        if (is_null($record['user_id']))
          $username = '';
        else
          $username = $record['user_firstname'] != '' ? $record['user_firstname'] : ( !is_null($record['oauth_user_name']) ? $record['oauth_user_name'] : $record['user_name'] );
        $response['page']['search']['results'][] = [
          'id' => $record['recipe_id'],
          'name' => $record['recipe_name'],
          'ownerId' => !is_null($record['user_id']) ? intval($record['user_id']) : 0,
          'ownerName' => $username,
          'description' => $record['recipe_description'],
          'eater' => intval($record['recipe_eater']),
          'pictureId' => !is_null($record['picture_id']) ? intval($record['picture_id']) : 0,
        ];
      }
    }

    return true;

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
      ->orderBy2('recipes', 'recipe_name', 'ASC')
      ->where('recipes', 'recipe_public', '=', 1)
      ->limit($Controller->Config()->DefaultListEntries());

    if (is_null($filter) || $filter == '')
      self::unfilteredList($response, $baseQuery, $countQuery);
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

    $count = $Controller->select($countQuery)->fetch_assoc()['count'];
    $result = $Controller->select($baseQuery);
    if (!$result) {
      $response = $Controller->Config()->getResponseArray(204);
      return false;
    }

    $data = [
      'count' => $count,
      'page' => 0,
      'pages' => ceil($count / $Controller->Config()->DefaultListEntries()),
      'itemsPerPage' => $Controller->Config()->DefaultListEntries(),
      'records' => []
    ];

    while ($record = $result->fetch_array()) {
      $recipe = new Recipe($record);
      if (!is_null($record['picture_id'])) {
        $picture = new Picture($record);
        $recipe->addPicture($picture);
      }
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

  static function filterPhrase(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery) : void {
    global $Controller;
    $basequery->andWhere('recipes', 'user_id', '=', $user->getId());
    $countquery->andWhere('recipes', 'user_id', '=', $user->getId());
    return;
  }

  static function unfilteredList(array &$response, QueryBuilder &$basequery, QueryBuilder &$countquery) : void {
    global $Controller;
    return;
  }

}
