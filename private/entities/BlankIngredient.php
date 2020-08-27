<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

class BlankIngredient implements IIngredient, IDbObject {

  private $id, $recipeid, $unit, $quantity, $description;
  private $changes = array();

  public function __construct(float $quantity, ?IUnit $unit, string $description) {
    $this->unit = $unit;
    $this->quantity = ($quantity != 0.0 ? $quantity : null);
    $this->description = $description;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getQuantity() : ?float {
    return $this->quantity;
  }

  public function getRecipe() : ?IRecipe {
    global $Controller;
    return $Controller->getRecipe($this->recipeid);
  }

  public function getRecipeId() : ?int {
    return $this->recipeid;
  }

  public function getUnit() : ?IUnit {
    return $this->unit;
  }

  public function getUnitId() : ?int {
    return $this->unit->getId();
  }

}
