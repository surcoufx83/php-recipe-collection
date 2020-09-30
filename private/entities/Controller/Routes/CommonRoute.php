<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class CommonRoute extends Route implements RouteInterface {

  private static $template = 'dummy';

  static function createOutput(array &$response) : bool {
    global $Controller;
    parent::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_home'));
    parent::setPage('private:home');
    parent::setTitle($Controller->l('greetings_hello', ''));
    return parent::render(self::$template, $response);
  }

}
