<?php

namespace Surcouf\Cookbook\Controller\Routes\User;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class ActivateRoute extends Route implements RouteInterface {

  private static $template = 'user/activation';
  private static $templateAuthNotRequired = 'user/activation-not-required';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    $token = $Controller->Dispatcher()->getFromMatches('token');
    $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
    $query->where('users', 'user_email_validation', 'LIKE', $token)
          ->andWhere('users', 'user_email_validated', 'IS NULL');
    $result = $Controller->select($query);
    if (is_null($result) || $result->num_rows == 0) {
      parent::addScript('auth-login');
      return parent::render(self::$templateAuthNotRequired, $response);
    }
    $user = $Controller->OM()->User($result->fetch_assoc());
    parent::addScript('activation');
    parent::addToDictionary('User', $user);
    return parent::render(self::$template, $response);
  }

}
