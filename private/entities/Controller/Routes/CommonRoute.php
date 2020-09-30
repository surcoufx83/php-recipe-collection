<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class CommonRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    return true;
  }

}
