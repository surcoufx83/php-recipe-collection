<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Helper\ConverterHelper;

if (!defined('CORE2'))
  exit;

class LoginSubmitRoute extends Route implements RouteInterface {

  private static $template = 'user/login';

  static function createOutput(array &$response) : bool {
    global $Controller;
    if ($Controller->isAuthenticated())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));

    $username = $Controller->Dispatcher()->getFromPayload('loginUsername');
    $password = $Controller->Dispatcher()->getFromPayload('loginPassword');
    $keepFlag = ConverterHelper::to_bool($Controller->Dispatcher()->getFromPayload('keepSession'));

    if (is_null($username) || is_null($password) || $username == '' || $password == '') {
      $response = $Controller->Config()->getResponseArray(30);
      return false;
    }

    if (!$Controller->loginWithPassword($username, $password, $keepFlag, $response)) {
      $Controller->Dispatcher()->exitJson($response);
      return false;
    }

    $response['isLoggedIn'] = $Controller->isAuthenticated();
    return true;
  }

}
