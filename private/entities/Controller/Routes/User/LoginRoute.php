<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class LoginRoute extends Route implements RouteInterface {

  private static $template = 'user/login';

  static function createOutput(array &$response) : bool {
    global $Controller;
    if ($Controller->isAuthenticated())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
    parent::addScript('auth-login');
    return parent::render(self::$template, $response);
  }

}
