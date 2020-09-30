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

    parent::addBreadcrumb($Controller->getLink('admin:main'), $Controller->l('breadcrumb_admin_home'));
    parent::addBreadcrumb($Controller->getLink('admin:users'), $Controller->l('breadcrumb_admin_user'));

    parent::addButton($Controller->getLink('admin:new-user'), $Controller->Config()->Icons()->Add('fa-fw mr-1').$Controller->l('page_admin_user_btnCreate'));

    parent::setPage('admin:main');
    parent::setSubPage('admin:users');
    parent::setTitle($Controller->l('page_admin_user_title'));
    return parent::render(self::$template, $response);
  }

}
