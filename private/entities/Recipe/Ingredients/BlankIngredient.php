<?php

namespace Surcouf\Cookbook\Recipe\Ingredients;

use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;

if (!defined('CORE2'))
  exit;

class BlankIngredient extends Ingredient {

  public function __construct(float $quantity, ?UnitInterface $unit, string $description) {
    $this->unit = $unit;
    $this->quantity = ($quantity != 0.0 ? $quantity : null);
    $this->description = $description;
  }

  public function getUnit() : ?UnitInterface {
    return $this->unit;
  }

  public function getUnitId() : ?int {
    if (!is_null($this->unit) && $this->unit->hasId())
      return $this->unit->getId();
    return null;
  }

}
