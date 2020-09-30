<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class OAuth2InitRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    if ($Controller->isAuthenticated())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
    $Controller->Dispatcher()->startOAuthLogin();
    return true;
  }

}
