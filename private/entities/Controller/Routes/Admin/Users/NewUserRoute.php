<?php

namespace Surcouf\Cookbook\Controller\Routes\Admin\Users;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class NewUserRoute extends Route implements RouteInterface {

  private static $template = 'admin/users/new-user';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    parent::addScript('admin-new-user');
    parent::addValidationScript();
    parent::setPage('admin:main');
    parent::setSubPage('admin:users');
    return parent::render(self::$template, $response);
  }

}
