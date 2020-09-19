<?php

namespace Surcouf\Cookbook\Recipe\Social\Tags;

if (!defined('CORE2'))
  exit;

class BlankTag extends Tag {

  public function __construct($name) {
    $this->tag_name = $name;
  }

  public function setId(int $newId) : TagInterface {
    $this->tag_id = $newId;
    return $this;
  }

}
