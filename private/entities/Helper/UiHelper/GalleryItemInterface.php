<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

interface GalleryItemInterface {

  public function setBody(string $title, string $text) : GalleryItemInterface;
  public function setFooterAction(string $title, ?string $url=null, ?string $id=null, ?string $cssClasses=null) : GalleryItemInterface;
  public function setFooterNote(string $title) : GalleryItemInterface;
  public function setImage(string $url, string $title='') : GalleryItemInterface;

}
