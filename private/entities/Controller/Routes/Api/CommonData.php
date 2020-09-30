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
      'currentRecipe' => null,
      'currentUser' => null,
      'contentData' => [
        'breadcrumbs' => []
      ]
    ];
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
      ]
    ];
    return true;
  }

}
