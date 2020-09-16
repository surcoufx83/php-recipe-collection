<?php

namespace Surcouf\Cookbook\Recipe\Social\Tags;

if (!defined('CORE2'))
  exit;

class BlankTag extends Tag {

  public function __construct($name) {
    $this->name = $name;
  }

  public function setId(int $newId) : TagInterface {
    $this->id = $newId;
    return $this;
  }

}
