<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;

if (!defined('CORE2'))
  exit;

interface RouteInterface {

  static function addBreadcrumb(string $Url, string $LinkText) : void;
  static function addButton(string $Url, string $LinkText, ?string $btnClass = 'btn-outline-blue') : void;
  static function addButtonScript(string $Id, string $LinkText, ?string $btnClass = 'btn-outline-blue') : void;
  static function addCarousel(array $carouselData) : void;
  static function addGallery(array $galleryData) : void;
  static function addScript(string $scriptName) : void;
  static function addRatingScript() : void;
  static function addToDictionary(string $key, $data) : void;
  static function addToPage(string $key, $data) : void;
  static function addValidationScript() : void;
  static function createOutput(array &$response) : bool;
  static function render(string $templateFile, array &$response) : bool;
  static function setPage(string $page) : void;
  static function setSubPage(string $subpage) : void;
  static function setTitle(string $newTitle) : void;

}
