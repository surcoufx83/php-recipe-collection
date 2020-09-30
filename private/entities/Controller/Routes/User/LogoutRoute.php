<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class LogoutRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $Controller->logout();
    $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
  }

}
