<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

class BlankTag implements ITag, IDbObject {

  private $id, $name;
  private $changes = array();

  public function __construct($name) {
    $this->name = $name;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName() : string {
    return $this->name;
  }

  public function setId(int $newId) : ITag {
    $this->id = $newId;
    return $this;
  }

}
