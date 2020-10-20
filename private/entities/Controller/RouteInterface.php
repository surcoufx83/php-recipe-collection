<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;

if (!defined('CORE2'))
  exit;

interface RouteInterface {

  static function addBreadcrumb(array &$response, string $pageTarget, string $linkText) : void;
  static function createOutput(array &$response) : bool;
  static function setDescription(array &$response, string $newDescription, ?array $arguments = null) : void;
  static function setTitle(array &$response, string $newTitle, ?array $arguments = null) : void;

}
