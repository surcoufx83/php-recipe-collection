<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class ProfileRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $payload = $Controller->Dispatcher()->getPayload();

    if (array_key_exists('update', $payload) && is_array($payload['update'])) {
      if (!$Controller->isAuthenticated()) {
        $response = $Controller->Config()->getResponseArray(110);
        return false;
      }
      return self::updateProfile($payload['update'], $response);
    }

    $response = $Controller->Config()->getResponseArray(71);
    return false;
  }

  static function updateProfile(array $request, array &$response) : bool {
    global $Controller;
    $user = $Controller->User();

    if (array_key_exists('email', $request)) {
      if ($request['email'] != '') {
        $newuser = $Controller->OM()->User($request['email']);
        if (!is_null($newuser) && $newuser->getId() != $user->getId()) {
          $response = $Controller->Config()->getResponseArray(401);
          return false;
        }
      }
      if ($user->setMail($request['email'])) {
        $response = $Controller->Config()->getResponseArray(1);
        return true;
      }
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    if (array_key_exists('firstname', $request)) {
      $user->setFirstname($request['firstname']);
      $response = $Controller->Config()->getResponseArray(1);
      return true;
    }

    if (array_key_exists('lastname', $request)) {
      $user->setLastname($request['lastname']);
      $response = $Controller->Config()->getResponseArray(1);
      return true;
    }

    $response = $Controller->Config()->getResponseArray(71);
    return false;

  }

}
