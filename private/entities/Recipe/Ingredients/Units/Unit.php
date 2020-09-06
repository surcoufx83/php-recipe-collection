<?php

namespace Surcouf\Cookbook\Recipe\Ingredients\Units;

use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class Unit implements UnitInterface, DbObjectInterface {

  protected $id, $name;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['unit_id']);
    $this->name = $dr['unit_name'];
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

  public function hasId() : bool {
    return !is_null($this->id);
  }

}
