<?php

namespace Surcouf\Cookbook\Recipe\Ingredients\Units;

if (!defined('CORE2'))
  exit;

class BlankUnit extends Unit {

  public function __construct(string $name) {
    $this->name = $name;
  }

  public function setId(int $newId) : UnitInterface {
    $this->id = $newId;
    return $this;
  }

}
