<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Admin;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class AdminPageRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $response = $Controller->Config()->getResponseArray(1);
    parent::addBreadcrumb($response, 'admin', $Controller->l('breadcrumb_admin_home'));
    return true;
  }

}
