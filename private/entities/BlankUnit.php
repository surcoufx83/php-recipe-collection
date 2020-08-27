<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

class BlankUnit extends Unit implements IUnit {

  private $id, $name;

  public function __construct(string $name) {
    $this->name = $name;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName() : string {
    return $this->name;
  }

  public function setId(int $newId) : BlankUnit {
    $this->id = $newId;
    return $this;
  }

}
