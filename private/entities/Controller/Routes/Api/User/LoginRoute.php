<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Helper\ConverterHelper;

if (!defined('CORE2'))
  exit;

class LoginRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $payload = $Controller->Dispatcher()->getPayload();
    if (!array_key_exists('userid', $payload) || !array_key_exists('userpwd', $payload)) {
      $response = $Controller->Config()->getResponseArray(81);
      return false;
    }

    $username = $payload['userid'];
    $password = $payload['userpwd'];
    $keep = array_key_exists('keepsession', $payload) ? ConverterHelper::to_bool($payload['keepsession']) : false;
    $result = $Controller->loginWithPassword($username, $password, $keep, $response);
    if ($result == true) {
      $user = $Controller->User();
      $response['user'] = [
        'avatar' => [
          'url' => $user->getAvatarUrl()
        ],
        'id' => $user->getId(),
        'isAdmin' => $user->isAdmin(),
        'loggedIn' => true,
        'meta' => [
          'fn' => $user->getFirstname(),
          'initials' => $user->getInitials(),
          'ln' => $user->getLastname(),
          'un' => $user->getUsername(),
        ]
      ];
    }
    return $result;

  }

}
