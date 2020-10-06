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
    $response['config'] = [
      'login' => [
        'defaultEnabled' => $Controller->Config()->PasswordLoginEnabled(),
        'oauth2Enabled' => $Controller->Config()->OAuth2Enabled(),
      ],
      'maintenanceEnabled' => $Controller->Config()->MaintenanceMode(),
    ];
    $response['page'] = [
      'currentRecipe' => new \stdClass,
      'currentUser' => new \stdClass,
      'contentData' => [
        'breadcrumbs' => [],
        'title' => '',
        'titleDescription' => ''
      ],
      'iconSet' => [
        'add' => ['icon' => 'plus-circle', 'space' => 'fas'],
        'info' => ['icon' => 'info-circle', 'space' => 'fas'],
        'like' => ['icon' => 'heart', 'space' => 'fas'],
        'spinner' => ['icon' => 'circle-notch', 'space' => 'fas'],
      ],
      'loading' => false,
      'myVote' => false,
      'routes' => [
        'sidebar' => [
          ['to' => 'home', 'icon' => 'home', 'text' => $Controller->l('sidebar_home') ],
          ['to' => 'writeRecipe', 'icon' => 'plus-circle', 'text' => $Controller->l('sidebar_recipe_write') ],
          ['to' => 'search', 'icon' => 'search', 'text' => $Controller->l('sidebar_recipe_search') ],
          ['to' => 'myRecipes', 'icon' => 'copy', 'text' => $Controller->l('sidebar_profile_recipes') ],
          ['to' => 'random', 'icon' => 'dice', 'text' => $Controller->l('sidebar_recipe_random') ],
        ]
      ],
      'sidebar' => [
        'visible' => true,
        'initialVisible' => true,
      ]
    ];
    if ($Controller->User()->isAdmin()) {
      $response['page']['routes']['sidebar'] = \array_merge_recursive($response['page']['routes']['sidebar'], [
        ['to' => 'admin', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_home'), 'children' => [
          ['to' => 'cronjobs', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_cronjobs')],
          ['to' => 'translations', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_translations')],
          ['to' => 'logs', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_logs')],
          ['to' => 'users', 'icon' => 'cogs', 'text' => $Controller->l('sidebar_admin_users')],
        ]],
      ]);
    }
    $response['user'] = [
      'avatar' => [
        'url' => $Controller->isAuthenticated() ? $Controller->User()->getAvatarUrl() : '',
      ],
      'loggedIn' => $Controller->isAuthenticated(),
      'id' => $Controller->isAuthenticated() ? $Controller->User()->getId() : '',
      'isAdmin' => $Controller->isAuthenticated() ? $Controller->User()->isAdmin() : false,
      'meta' => [
        'fn' => $Controller->isAuthenticated() ? $Controller->User()->getFirstname() : '',
        'ln' => $Controller->isAuthenticated() ? $Controller->User()->getLastname() : '',
        'un' => $Controller->isAuthenticated() ? $Controller->User()->getUsername() : '',
        'initials' => $Controller->isAuthenticated() ? $Controller->User()->getInitials() : '',
      ],
      'settings' => [
        'formatters' => [
          'dateFormat' => $Controller->Config()->DefaultDateFormatUi(),
          'dateTimeFormat' => $Controller->Config()->DefaultDateTimeFormat(),
          'longDateFormat' => $Controller->Config()->DefaultLongDateTimeFormat(),
          'timeFormat' => $Controller->Config()->DefaultTimeFormat(),
          'decimals' => $Controller->Config()->DefaultDecimalsCount(),
          'decimalsSeparator' => $Controller->Config()->DefaultDecimalsSeparator(),
          'thousandsSeparator' => $Controller->Config()->DefaultThousandsSeparator(),
        ],
        'views' => [
          'listLength' => $Controller->Config()->DefaultListEntries(),
        ]
      ],
    ];
    return true;
  }

}
