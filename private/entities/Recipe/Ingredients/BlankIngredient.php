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

}