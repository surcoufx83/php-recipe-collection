<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class ActivateAccountRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    $token = $Controller->Dispatcher()->getFromMatches('token');
    $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
    $query->where('users', 'user_email_validation', 'LIKE', $token)
          ->andWhere('users', 'user_email_validated', 'IS NULL');
    $result = $Controller->select($query);
    if (is_null($result) || $result->num_rows == 0) {
      $response = $Controller->Config()->getResponseArray(11);
      return false;
    }
    $payload = $Controller->Dispatcher()->getPayload();
    $user = $Controller->OM()->User($result->fetch_assoc());

    if (!array_key_exists('user', $payload) || intval($payload['user'] != $user->getId())) {
      $response = $Controller->Config()->getResponseArray(11);
      return false;
    }

    if (!array_key_exists('password1', $payload) || !array_key_exists('password2', $payload) ||
      $payload['password1'] != $payload['password2']) {
        $response = $Controller->Config()->getResponseArray(11);
        return false;
      }

    $keepSession = false;
    if (array_key_exists('keepSession', $payload))
      $keepSession = ConverterHelper::to_bool($payload['keepSession']);

    $response = null;
    if ($user->setPassword($payload['password1'], '')) {
      $user->validateEmail($token);
      $Controller->loginWithPassword($user->getMail(), $payload['password1'], $keepSession, $response);
    }
    else
      $response = $Controller->Config()->getResponseArray(30);
    $response['isLoggedIn'] = $Controller->isAuthenticated();
    return true;

  }

}
