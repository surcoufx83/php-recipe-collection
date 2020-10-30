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
    parent::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_home'));
    parent::setPage('admin:main');
    parent::setSubPage('admin:main');
    parent::setTitle($Controller->l('page_admin_dashboard_title'));
    return parent::render(self::$template, $response);
  }

}
