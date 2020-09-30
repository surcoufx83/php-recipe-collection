<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class SelfRegisterRoute extends Route implements RouteInterface {

  private static $template = 'user/login';

  static function createOutput(array &$response) : bool {
    global $Controller;
    if ($Controller->Dispatcher()->queryOAuthUserData())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
  }

}
