<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Request\ERequestMethod;
use Surcouf\Cookbook\Response\EOutputMode;

if (!defined('CORE2'))
  exit;

final class RoutingManager {

  static $routes = [
    '/api/login' => [ // login request
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\User\LoginRoute::class,
      'method' => ERequestMethod::HTTP_POST,
      'requiresUser' => false,
    ],
    '/api/logout' => [ // logout request
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\User\LogoutRoute::class,
      'method' => ERequestMethod::HTTP_POST,
      'output' => EOutputMode::JSON,
      'requiresPayload' => [
        'user', 'password1', 'password2', 'keepSession'
      ],
      'requiresUser' => false,
    ],
    '/admin' => [ // list of users recipes
      'class' => \Surcouf\Cookbook\Controller\Routes\Admin\AdminHomeRoute::class,
      'requiresAdmin' => true,
    ],
    '/admin/new-user' => [
        [ // new user page
        'class' => \Surcouf\Cookbook\Controller\Routes\Admin\Users\NewUserRoute::class,
        'requiresAdmin' => true,
      ],
      [ // post new user page
        'class' => \Surcouf\Cookbook\Controller\Routes\Admin\Users\NewUserPostRoute::class,
        'method' => ERequestMethod::HTTP_POST,
        'output' => EOutputMode::JSON,
        'requiresAdmin' => true,
        'requiresPayload' => [
          'firstname', 'lastname', 'email', 'username'
        ],
      ],
    ],
    '/admin/test/entity' => [ // ajax query for existing entity
      'class' => \Surcouf\Cookbook\Controller\Routes\Admin\TestEntityRoute::class,
      'method' => ERequestMethod::HTTP_POST,
      'output' => EOutputMode::JSON,
      'requiresAdmin' => true,
    ],
    '/admin/users' => [ // list of users recipes
      'class' => \Surcouf\Cookbook\Controller\Routes\Admin\Users\UsersRoute::class,
      'requiresAdmin' => true,
    ],
    '/books' => [ // list of users books
      'class' => \Surcouf\Cookbook\Controller\Routes\CommonRoute::class,
    ],
    '/api/page-data\?/random(/-(?<id>\d+))?' => [ // random recipe page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RandomRecipePageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
    ],
    '/api/page-data\?/recipe/(?<id>\d+)-(?<name>[^/]+)(/(?<action>[^/]+))?' => [ // recipe page
      [ // recipe display page
        'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RecipePageRoute::class,
        'method' => ERequestMethod::HTTP_GET,
        'createObject' => [
          'idkey' => 'id',
          'method' => 'Recipe',
        ],
      ],
      [ // post login
        'class' => \Surcouf\Cookbook\Controller\Routes\User\LoginSubmitRoute::class,
        'method' => ERequestMethod::HTTP_POST,
        'output' => EOutputMode::JSON,
        'requiresPayload' => [
          'loginUsername', 'loginPassword', 'keepSession'
        ],
        'requiresUser' => false,
      ]
    ],
    '/api/page-data\?/search' => [ // search query
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Search\SearchResultsRoute::class,
      'method' => ERequestMethod::HTTP_POST,
    ],
    '/api/page-data\?(/recipes(/(?<filter>[^/]+)(/(?<id>\d+)-(?<name>.+))?)?|(?<home>/(home)?))' => [ // recipe listing page with filter
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RecipeListRoute::class,
      'method' => ERequestMethod::HTTP_GET,
    ],
    '/maintenance' => [ // maintenance page
      'class' => \Surcouf\Cookbook\Controller\Routes\MaintenanceRoute::class,
      'ignoreMaintenance' => true,
      'requiresUser' => false,
    ],
    '/myrecipes' => [ // list of users recipes
      'class' => \Surcouf\Cookbook\Controller\Routes\User\RecipesRoute::class,
    ],
    '/oauth2/callback\?[^/]+' => [ // callback from oauth server
      'class' => \Surcouf\Cookbook\Controller\Routes\User\OAuth2CallbackRoute::class,
      'requiresUser' => false,
    ],
    '/oauth2/login(\?)?' => [ // init oauth login
      'class' => \Surcouf\Cookbook\Controller\Routes\User\OAuth2InitRoute::class,
      'requiresUser' => false,
    ],
    '/random' => [ // random recipe
      'class' => \Surcouf\Cookbook\Controller\Routes\RandomRecipeRoute::class,
    ],
    '/recipe/new' => [
        [ // new recipe page
        'class' => \Surcouf\Cookbook\Controller\Routes\Recipe\RecipeNewRoute::class,
      ],
      [ // post new recipe page
        'class' => \Surcouf\Cookbook\Controller\Routes\Recipe\RecipeNewSubmitRoute::class,
        'method' => ERequestMethod::HTTP_POST,
        'output' => EOutputMode::JSON,
        'requiresPayload' => [
          'description', 'eater', 'name', 'ingredient_description',
          'ingredient_unit', 'ingredient_quantity', 'step_description', 'step_title',
          'step_duration_preparation', 'step_duration_cooking', 'step_duration_rest'
        ],
      ],
    ],
    '/recipe/(un)?publish/(?<id>\d+)(/[^/]+)?' => [ // recipe publish/unpublish page
      'class' => \Surcouf\Cookbook\Controller\Routes\Recipe\RecipePublishRoute::class,
      'createObject' => [
        'idkey' => 'id',
        'method' => 'Recipe',
      ],
    ],
    '/recipe/vote/(?<id>\d+)(/[^/]+)?' => [ // recipe post vote
      'class' => \Surcouf\Cookbook\Controller\Routes\Recipe\RecipVoteRoute::class,
      'createObject' => [
        'idkey' => 'id',
        'method' => 'Recipe',
      ],
      'method' => ERequestMethod::HTTP_POST,
      'output' => EOutputMode::JSON,
      'requiresPayload' => [
        'cooked', 'rated', 'voted'
      ],
    ],
    '/self-register' => [ // dummy page after oauth login
      'class' => \Surcouf\Cookbook\Controller\Routes\User\SelfRegisterRoute::class,
    ],
    '/.*' => [ // fallback to dummy page if no match
      'class' => \Surcouf\Cookbook\Controller\Routes\CommonRoute::class,
      'requiresUser' => false,
    ],
  ];

  static function registerRoutes() : bool {
    global $Controller;

    foreach (self::$routes as $key => $data) {
      if (array_key_exists('class', $data)) {
        if ($Controller->Dispatcher()->addRoute($key, $data))
          return true;
      } else {
        for ($i=0; $i<count($data); $i++) {
          if ($Controller->Dispatcher()->addRoute($key, $data[$i]))
            return true;
        }
      }
    }

    return false;

  }

}
