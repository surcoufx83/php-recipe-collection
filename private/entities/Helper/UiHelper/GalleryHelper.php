<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

final class GalleryHelper implements GalleryHelperInterface {

  private $columncount = 3;
  private $items = [];

  public function __construct(int $ColumnCount = 3) {
    $this->columncount = $ColumnCount;
  }

  public function addItem(GalleryItemInterface $item) : GalleryHelperInterface {
    $this->items[] = $item;
    return $this;
  }

  public function getItems() : array {
    return $this->items;
  }

  public function render() : string {
    global $Controller, $twig;
    return $twig->render('views/common/gallery.html.twig', [
      'Gallery' => $this,
      'Controller' => $Controller,
      'Config' => $Controller->Config()
    ]);
  }

}
