<?php

namespace Surcouf\Cookbook\Controller\Routes\Admin\Users;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\User\BlankUser;

if (!defined('CORE2'))
  exit;

class NewUserPostRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $payload = $Controller->Dispatcher()->getPayload();

    if ($payload['firstname'] == '' || is_null($payload['firstname'])) {
      $response = $Controller->Config()->getResponseArray(80);
      return $response;
    }

    if ($payload['lastname'] == '' || is_null($payload['lastname'])) {
      $response = $Controller->Config()->getResponseArray(80);
      return $response;
    }

    if ($payload['email'] == '' || is_null($payload['email'])) {
      $response = $Controller->Config()->getResponseArray(80);
      return $response;
    }

    if ($payload['username'] == '' || is_null($payload['username'])) {
      $response = $Controller->Config()->getResponseArray(80);
      return $response;
    }

    $user = new BlankUser($payload['firstname'], $payload['lastname'], $payload['username'], $payload['email']);
    $response = [];
    if (!$user->save($response))
      return false;
    if (!$user->sendActivationMail($response))
      return false;

    $response = $Controller->Config()->getResponseArray(1);
    $response['userid'] = $user->getId();
    return true;

  }

}
