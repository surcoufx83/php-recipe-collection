<?php

namespace Surcouf\Cookbook\Recipe\Ingredients\Units;

use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class Unit implements UnitInterface, DbObjectInterface, \JsonSerializable {

  protected $unit_id,
            $unit_name;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->unit_id = intval($record['unit_id']);
      $this->unit_name = $record['unit_name'];
    } else {
      $this->unit_id = intval($this->unit_id);
    }
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->unit_id;
  }

  public function getName() : string {
    return $this->unit_name;
  }

  public function hasId() : bool {
    return !is_null($this->unit_id);
  }

  public function jsonSerialize() {
    return [
      'id' => $this->unit_id,
      'name' => $this->unit_name,
    ];
  }

}
