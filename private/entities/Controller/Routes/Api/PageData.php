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
    self::$page($response);
    return true;
  }

  public static function __callStatic(string $methodName, array $params) : void {
    return;
  }

}
