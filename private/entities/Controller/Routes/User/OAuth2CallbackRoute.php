<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class OAuth2CallbackRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    if ($Controller->isAuthenticated()) // if already logged in -> show homepage
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
    $response = $Controller->Config()->getResponseArray(32);
    return $Controller->Dispatcher()->finishOAuthLogin($response); // in case of success -> dispatcher will forward the user
  }

}
