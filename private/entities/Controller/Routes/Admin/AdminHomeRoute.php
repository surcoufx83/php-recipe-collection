<?php

namespace Surcouf\Cookbook\Controller\Routes\Admin;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class AdminHomeRoute extends Route implements RouteInterface {

  private static $template = 'dummy';

  static function createOutput(array &$response) : bool {
    global $Controller;
    parent::setPage('admin:main');
    parent::setSubPage('admin:main');
    return parent::render(self::$template, $response);
  }

}
