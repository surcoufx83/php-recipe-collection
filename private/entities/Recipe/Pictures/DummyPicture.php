<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

if (!defined('CORE2'))
  exit;

class DummyPicture extends Picture implements \JsonSerializable {

  public function __construct(int $index) {
    global $Controller;
    $this->picture_sortindex = $index;
    $this->picture_name = 'Kein Bild vorhanden';
    $this->picture_filename = '_dummy.jpg';
  }

  public function jsonSerialize() {
    global $Controller;
    return [
      'description' => '',
      'id' => 0,
      'index' => $this->picture_sortindex,
      'link' => '/pictures/_dummy.jpg',
      'link350' => '/pictures/_dummy0x350.jpg',
      'name' => $this->picture_name,
      'uploadFile' => null,
      'uploaderId' => 0,
      'uploaderName' => '',
    ];
  }

}
