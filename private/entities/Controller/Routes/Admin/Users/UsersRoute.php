<?php

namespace Surcouf\Cookbook\Controller\Routes\Admin\Users;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Recipe\Recipe;

if (!defined('CORE2'))
  exit;

class UsersRoute extends Route implements RouteInterface {

  private static $template = 'dummy';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;
    parent::setPage('admin:main');
    parent::setSubPage('admin:users');
    return parent::render(self::$template, $response);
  }

}
