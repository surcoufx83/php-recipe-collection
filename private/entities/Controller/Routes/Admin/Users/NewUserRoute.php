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

    parent::addBreadcrumb($Controller->getLink('admin:main'), $Controller->l('breadcrumb_admin_home'));
    parent::addBreadcrumb($Controller->getLink('admin:users'), $Controller->l('breadcrumb_admin_user'));
    parent::addBreadcrumb($Controller->getLink('admin:new-user'), $Controller->l('breadcrumb_admin_newUser'));

    parent::addScript('admin-new-user');
    parent::addValidationScript();
    parent::setPage('admin:main');
    parent::setSubPage('admin:users');
    parent::setTitle($Controller->l('page_admin_newUser_title'));
    return parent::render(self::$template, $response);
  }

}
