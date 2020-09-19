<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;

if (!defined('CORE2'))
  exit;

class Route implements RouteInterface {

  private static $template = 'dummy';

  static function addBreadcrumb(string $Url, string $LinkText) : void {
    global $OUT;
    $OUT['Page']['Breadcrumbs'][] = array(
      'text' => $LinkText,
      'url' => $Url,
    );
  }

  static function addButton(string $Url, string $LinkText, ?string $btnClass = 'btn-outline-blue') : void {
    global $OUT;
    $OUT['Page']['Actions'][] = array(
      'class' => $btnClass,
      'text' => $LinkText,
      'url' => $Url,
    );
  }

  static function addButtonScript(string $Id, string $LinkText, ?string $btnClass = 'btn-outline-blue') : void {
    global $OUT;
    $OUT['Page']['Actions'][] = array(
      'class' => $btnClass,
      'text' => $LinkText,
      'id' => $Id,
    );
  }

  static function addCarousel(array $carouselData) : void {
    global $OUT;
    $OUT['Page']['Carousel'] = CarouselHelper::render($carouselData);
  }

  static function addScript(string $scriptName) : void {
    global $OUT;
    $OUT['Page']['Scripts']['Custom'][] = $scriptName;
  }

  static function addRatingScript() : void {
    global $OUT;
    $OUT['Page']['Scripts']['StarRating'] = true;
  }

  static function addToDictionary(string $key, $data) : void {
    global $OUT;
    $OUT[$key] = $data;
  }

  static function addToPage(string $key, $data) : void {
    global $OUT;
    $OUT['Page'][$key] = $data;
  }

  static function addValidationScript() : void {
    global $OUT;
    $OUT['Page']['Scripts']['FormValidator'] = true;
  }

  static function createOutput(array &$response) : bool {
    self::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_home'));
    self::setPage('private:home');
    self::setTitle($Controller->l('greetings_hello', ''));
    return self::render(self::$template, $response);
  }

  static function render(string $templateFile, array &$response) : bool {
    global $OUT, $twig;
    $OUT['Content'] = $twig->render(sprintf('views/%s.html.twig', $templateFile), $OUT);
    return true;
  }

  static function setPage(string $page) : void {
    global $OUT;
    $OUT['Page']['Current'] = $page;
  }

  static function setSubPage(string $subpage) : void {
    global $OUT;
    $OUT['Page']['CurrentSub'] = $subpage;
  }

  static function setTitle(string $newTitle) : void {
    global $OUT;
    $OUT['Page']['Heading1'] = $newTitle;
  }

}
