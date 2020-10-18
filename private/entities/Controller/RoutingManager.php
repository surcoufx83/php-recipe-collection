<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Request\ERequestMethod;
use Surcouf\Cookbook\Response\EOutputMode;

if (!defined('CORE2'))
  exit;

final class RoutingManager {

  static $routes = [
    '/api/common-data' => [ // common data request
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\CommonData::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresUser' => false,
    ],
    '/api/logout' => [ // common data request
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\User\LogoutRoute::class,
      'method' => ERequestMethod::HTTP_POST,
      'requiresUser' => false,
    ],
    '/api/page-data\?/admin' => [ // administration page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Admin\AdminPageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresAdmin' => true,
    ],
    '/api/page-data\?/admin/cronjobs' => [ // cronjobs administration page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Admin\CronjobsPageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresAdmin' => true,
    ],
    '/api/page-data\?/admin/logs' => [ // logs administration page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Admin\LogsPageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresAdmin' => true,
    ],
    '/api/page-data\?/admin/translations' => [ // translations administration page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Admin\TranslationsPageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresAdmin' => true,
    ],
    '/api/page-data\?/admin/users' => [ // user administration page
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Admin\UsersPageRoute::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresAdmin' => true,
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
      [ // recipe actions (vote, publish, etc)
        'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RecipePostRoute::class,
        'method' => ERequestMethod::HTTP_POST,
        'createObject' => [
          'idkey' => 'id',
          'method' => 'Recipe',
        ],
      ]
    ],
    '/api/page-data\?/search' => [ // search query
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Search\SearchResultsRoute::class,
      'method' => ERequestMethod::HTTP_POST,
    ],
    '/api/page-data\?/recipes(/(?<filter>[^/]+)(/(?<id>\d+)-(?<name>.+))?)?' => [ // recipe listing page with filter
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RecipeListRoute::class,
      'method' => ERequestMethod::HTTP_GET,
    ],
    '/api/page-data\?/write' => [ // post new recipe
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\Recipe\RecipeCreateRoute::class,
      'method' => ERequestMethod::HTTP_POST,
    ],
    '/api/page-data\?(?<page>.*)' => [ // common data request
      'class' => \Surcouf\Cookbook\Controller\Routes\Api\PageData::class,
      'method' => ERequestMethod::HTTP_GET,
      'requiresUser' => false,
    ],
    '/oauth2/callback\?[^/]+' => [ // callback from oauth server
      'class' => \Surcouf\Cookbook\Controller\Routes\User\OAuth2CallbackRoute::class,
      'requiresUser' => false,
    ],
    '/oauth2/login(\?)?' => [ // init oauth login
      'class' => \Surcouf\Cookbook\Controller\Routes\User\OAuth2InitRoute::class,
      'requiresUser' => false,
    ],
    '/.*' => [
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
