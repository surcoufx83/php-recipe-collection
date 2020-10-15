<?php

namespace Surcouf\Cookbook\Controller\Routes\Api;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class PageData extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $response = $Controller->Config()->getResponseArray(1);
    $page = 'createOutput_'.str_replace('/', '_', $Controller->Dispatcher()->getMatches()['page']);
    if (!self::$page($response)) {
      parent::addBreadcrumb($response, 'home', $Controller->l('breadcrumb_home'));
      parent::setTitle($response, 'Seite noch nicht programmiert');
      parent::setDescription($response, 'Die aufgerufene Seite ist noch nicht fertig gestellt, Stefan ist einfach zu faul. Probiere einen der Links aus der Navigation (am Handy auf das Balken-Menü links oben doppelt klicken), ich glaube "Zufälliges Rezept geht schon".');
    }
    return true;
  }

  public static function __callStatic(string $methodName, array $params) : bool {
    return false;
  }

  public static function createOutput__search(array &$response) : bool {
    global $Controller;
    parent::addBreadcrumb($response, 'search', $Controller->l('breadcrumb_search_main'));
    parent::setTitle($response, $Controller->l('search_title'));
    return true;
  }

  public static function createOutput__write(array &$response) : bool {
    global $Controller;
    parent::addBreadcrumb($response, 'writeRecipe', $Controller->l('breadcrumb_newRecipe'));
    parent::setTitle($response, $Controller->l('newRecipe_header'));
    return true;
  }

}
