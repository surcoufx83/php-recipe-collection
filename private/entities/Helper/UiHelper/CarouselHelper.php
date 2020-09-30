<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

final class CarouselHelper implements ICarouselHelper {

  public static function addItem(array &$carousel, array $record) : void {
    $first = (count($carousel['items']) == 0);
    $carousel['items'][] = [
      'active' => ($first ? 'active' : ''),
      'image' => $record['image'],
      'href' => $record['href'],
      'title' => $record['title'],
      'description' => $record['description'],
    ];
    $carousel['targets'][] = [
      'active' => ($first ? 'active' : ''),
      'index' => count($carousel['targets']),
    ];
  }

  public static function createNew(string $id, bool $smallCarousel = false, bool $enableLightbox = false) : array {
    $carousel = [
      'id' => $id,
      'small' => $smallCarousel,
      'lightbox' => $enableLightbox,
      'items' => [],
      'targets' => [],
    ];
    return ($carousel);
  }

  public static function render(array &$carousel) : string {
    global $twig;
    return $twig->render('views/common/carousel.html.twig', ['carousel' => $carousel]);
  }

}
