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
    parent::addBreadcrumb($response, 'home', $Controller->l('breadcrumb_home'));
    parent::setTitle($response, 'Seite noch nicht programmiert');
    parent::setDescription($response, 'Die aufgerufene Seite ist noch nicht fertig gestellt, Stefan ist einfach zu faul. Probiere einen der Links aus der Navigation (am Handy auf das Balken-Menü links oben doppelt klicken), ich glaube "Zufälliges Rezept geht schon".');
    $page = 'createOutput_'.str_replace('/', '_', $Controller->Dispatcher()->getMatches()['page']);
    self::$page($response);
    return true;
  }

  public static function __callStatic(string $methodName, array $params) : void {
    return;
  }

}
