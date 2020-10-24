<?php

namespace Surcouf\Cookbook\Controller\Routes\Api;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class CommonData extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $response = $Controller->Config()->getResponseArray(1);
    self::addConfig($response);
    self::addForwarding($response);
    self::addPagedata($response);
    self::addUserdata($response);

    return true;
  }

  private static function addConfig(array &$response) : void {
    global $Controller;
    parent::addToDictionary($response, ['config' => [
      'login' => [
        'defaultEnabled' => $Controller->Config()->Users('LoginMethods', 'Password'),
        'oauth2Enabled' => $Controller->Config()->Users('LoginMethods', 'OAuth2'),
      ],
      'maintenanceEnabled' => $Controller->Config()->System('MaintenanceMode'),
    ]]);
  }

  private static function addForwarding(array &$response) : void {
    global $Controller;
    parent::addToDictionary($response, ['forward' => false]);
  }

  private static function addPagedata(array &$response) : void {
    global $Controller;
    parent::addToDictionary($response, ['page' => [
      'currentRecipe' => new \stdClass,
      'currentUser' => new \stdClass,
      'contentData' => [
        'actions' => [],
        'breadcrumbs' => [],
        'filters' => [],
        'hasActions' => false,
        'hasFilters' => false,
      ],
      'customContent' => false,
      'iconSet' => self::getIconSet(),
      'loading' => false,
      'updating' => false,
      'modals' => [
        'failedModal' => [
          'message' => '',
          'code' => 0,
        ]
      ],
      'search' => [
        'filter' => [
          'global' => '',
          'ingredients' => '',
          'maxTime' => '',
          'options' => [
            'onlyWithComments' => false,
            'onlyWithPic' => false
          ],
          'rating' => -1,
          'sortBy' => '',
          'tags' => '',
          'title' => '',
          'user' => '',
          'voting' => -1,
        ],
        'records' => [
          'total' => 0,
          'numpages' => 0,
          'page' => 1,
        ],
        'results' => [],
      ],
      'self' => [
        'currentVote' => [
          'cooked' => -1,
          'rating' => -1,
          'voting' => -1,
        ],
        'hasVoted' => false,
        'lastVote' => [
          'id' => '',
          'userId' => '',
          'user' => '',
          'time' => '',
          'comment' => '',
          'cooked' => '',
          'voting' => '',
          'rating' => '',
          'formatted' => [
            'time' => '',
          ]
        ],
        'visitCount' => 0,
        'voteCount' => 0,
      ],
      'routes' => [
        'sidebar' => self::getSidebarRoutes(),
      ],
      'sidebar' => [
        'visible' => true,
        'initialVisible' => true,
      ]
    ]]);
  }

  private static function addUserdata(array &$response) : void {
    global $Controller;
    if ($Controller->isAuthenticated()) {
      parent::addToDictionary($response, ['user' => [
        'avatar' => [
          'url' => $Controller->User()->getAvatarUrl(),
        ],
        'loggedIn' => $Controller->isAuthenticated(),
        'id' => $Controller->User()->getId(),
        'isAdmin' => $Controller->User()->isAdmin(),
        'meta' => [
          'fn' => $Controller->User()->getFirstname(),
          'ln' => $Controller->User()->getLastname(),
          'un' => $Controller->User()->getUsername(),
          'initials' => $Controller->User()->getInitials(),
        ],
        'settings' => [
          'formatters' => [
            'dateFormat' => $Controller->Config()->Defaults('Formats', 'UiShortDate'),
            'dateTimeFormat' => $Controller->Config()->Defaults('Formats', 'UiShortDatetime'),
            'longDateFormat' => $Controller->Config()->Defaults('Formats', 'UiLongDatetime'),
            'timeFormat' => $Controller->Config()->Defaults('Formats', 'UiTime'),
            'decimals' => $Controller->Config()->Defaults('Formats', 'Decimals'),
            'decimalsSeparator' => $Controller->Config()->Defaults('Formats', 'DecimalsSeparator'),
            'thousandsSeparator' => $Controller->Config()->Defaults('Formats', 'ThousandsSeparator'),
          ],
          'views' => [
            'listLength' => $Controller->Config()->Defaults('Lists', 'Entries'),
          ]
        ],
      ]]);
    } else {
      parent::addToDictionary($response, ['user' => [
        'avatar' => [
          'url' => '',
        ],
        'loggedIn' => $Controller->isAuthenticated(),
        'id' => 0,
        'isAdmin' => false,
        'meta' => [
          'fn' => '',
          'ln' => '',
          'un' => '',
          'initials' => '',
        ],
        'settings' => [
          'formatters' => [
            'dateFormat' => $Controller->Config()->Defaults('Formats', 'UiShortDate'),
            'dateTimeFormat' => $Controller->Config()->Defaults('Formats', 'UiShortDatetime'),
            'longDateFormat' => $Controller->Config()->Defaults('Formats', 'UiLongDatetime'),
            'timeFormat' => $Controller->Config()->Defaults('Formats', 'UiTime'),
            'decimals' => $Controller->Config()->Defaults('Formats', 'Decimals'),
            'decimalsSeparator' => $Controller->Config()->Defaults('Formats', 'DecimalsSeparator'),
            'thousandsSeparator' => $Controller->Config()->Defaults('Formats', 'ThousandsSeparator'),
          ],
          'views' => [
            'listLength' => $Controller->Config()->Defaults('Lists', 'Entries'),
          ]
        ],
      ]]);
    }
  }

  private static function getIconSet() : array {
    return [
      'add' => ['icon' => 'plus-circle', 'space' => 'fas'],
      'back' => ['icon' => 'arrow-left', 'space' => 'fas'],
      'delete' => ['icon' => 'trash-alt', 'space' => 'far'],
      'edit' => ['icon' => 'edit', 'space' => 'fas'],
      'gallery' => ['icon' => 'camera-retro', 'space' => 'fas'],
      'info' => ['icon' => 'info-circle', 'space' => 'fas'],
      'ingredient' => ['icon' => 'carrot', 'space' => 'fas'],
      'like' => ['icon' => 'heart', 'space' => 'fas'],
      'lock' => ['icon' => 'lock', 'space' => 'fas'],
      'meal' => ['icon' => 'utensils', 'space' => 'fas'],
      'menu' => ['icon' => 'bars', 'space' => 'fas'],
      'msg' => ['icon' => 'comment-dots', 'space' => 'fas'],
      'nouser' => ['icon' => 'user-slash', 'space' => 'fas'],
      'play' => ['icon' => 'play', 'space' => 'fas'],
      'random' => ['icon' => 'dice', 'space' => 'fas'],
      'reset' => ['icon' => 'times-circle', 'space' => 'fas'],
      'search' => ['icon' => 'search', 'space' => 'fas'],
      'spinner' => ['icon' => 'circle-notch', 'space' => 'fas'],
      'unlock' => ['icon' => 'unlock', 'space' => 'fas'],
      'user' => ['icon' => 'user-circle', 'space' => 'far'],
      'view' => ['icon' => 'eye', 'space' => 'fas'],
    ];
  }

  private static function getSidebarRoutes() : array {
    global $Controller;
    $routes = [
      ['to' => 'home', 'icon' => 'home', 'text' => $Controller->l('sidebar_home') ],
      ['to' => 'writeRecipe', 'icon' => 'plus-circle', 'text' => $Controller->l('sidebar_recipe_write') ],
      ['to' => 'search', 'icon' => 'search', 'text' => $Controller->l('sidebar_recipe_search') ],
      ['to' => 'myRecipes', 'icon' => 'copy', 'text' => $Controller->l('sidebar_profile_recipes') ],
      ['to' => 'random', 'icon' => 'dice', 'text' => $Controller->l('sidebar_recipe_random') ],
    ];
    if (!is_null($Controller->User()) && $Controller->User()->isAdmin()) {
      $routes = \array_merge_recursive($routes, [
        ['to' => 'admin', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_home'), 'children' => [
          ['to' => 'cronjobs', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_cronjobs')],
          ['to' => 'translations', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_translations')],
          ['to' => 'logs', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_logs')],
          ['to' => 'users', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_users')],
        ]],
      ]);
    }
    return $routes;
  }

}
