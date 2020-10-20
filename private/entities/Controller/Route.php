<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;
use Surcouf\Cookbook\Helper\UiHelper\GalleryHelper;
use Surcouf\Cookbook\Recipe\RecipeInterface;

if (!defined('CORE2'))
  exit;

class Route implements RouteInterface {

  private static $template = 'dummy';

  static function addBreadcrumb(array &$response, string $pageTarget, string $linkText, ?array $params = []) : void {
    $response = array_merge_recursive($response, [
      'page' => ['contentData' => [
          'breadcrumbs' => [
            [ 'target' => $pageTarget, 'title' => $linkText, 'params' => $params]
          ]
      ]]]);
  }

  static function addToDictionary(array &$response, array $dataToAdd) : void {
    $response = array_merge_recursive($response, $dataToAdd);
  }

  static function createOutput(array &$response) : bool {
    self::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_home'));
    self::setPage('private:home');
    self::setTitle($Controller->l('greetings_hello', ''));
    return self::render(self::$template, $response);
  }

  static function forwardResponse(array &$response, string $pageTarget, ?array $params = []) : void {
    $response = array_merge_recursive($response, [
      'forward' => [
        'route' => $pageTarget,
        'params' => $params,
        ]]);
  }

  static function forwardExternalResponse(array &$response, string $url) : void {
    $response = array_merge_recursive($response, [
      'forward' => [
        'ext' => true,
        'extUrl' => $url,
        ]]);
  }

  static function setDescription(array &$response, string $newDescription, ?array $arguments = null) : void {
    $response = \array_merge_recursive($response, ['page' => ['contentData' => [
                  'titleDescription' => (is_null($arguments) ? $newDescription : vsprintf($newDescription, $arguments))
                ]]]);
  }

  static function setTitle(array &$response, string $newTitle, ?array $arguments = null) : void {
    $response = \array_merge_recursive($response, ['page' => ['contentData' => [
                  'title' => (is_null($arguments) ? $newTitle : vsprintf($newTitle, $arguments))
                ]]]);
  }

}
