<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class MaintenanceRoute extends Route implements RouteInterface {

  private static $template = 'maintenance';

  static function createOutput(array &$response) : bool {
    global $Controller;
    if (!MAINTENANCE)
      $Controller->Dispatcher()->forwardTo('/');
    return parent::render(self::$template, $response);
  }

}
