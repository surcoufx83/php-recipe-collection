<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

interface GalleryHelperInterface {

  public function addItem(GalleryItemInterface $item) : GalleryHelperInterface;
  public function getItems() : array;
  public function render() : string;

}
