<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class CommonRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $response = $Controller->Config()->getResponseArray(1);
    parent::addBreadcrumb($response, 'home', $Controller->l('breadcrumb_home'));
    parent::setTitle($response,  'Page not found');
    return true;
  }

}
