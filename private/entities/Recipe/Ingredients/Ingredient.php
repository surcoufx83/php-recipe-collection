<?php

namespace Surcouf\Cookbook\Recipe\Ingredients;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;

if (!defined('CORE2'))
  exit;

class Ingredient implements IngredientInterface, DbObjectInterface, \JsonSerializable {

  protected $ingredient_id,
            $recipe_id,
            $unit_id,
            $ingredient_quantity,
            $ingredient_description;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->ingredient_id = intval($record['ingredient_id']);
      $this->recipe_id = intval($record['recipe_id']);
      $this->unit_id = (!is_null($record['unit_id']) ? intval($record['unit_id']) : null);
      $this->ingredient_quantity = (!is_null($record['ingredient_quantity']) ? floatval($record['ingredient_quantity']) : null);
      $this->ingredient_description = $record['ingredient_description'];
    } else {
      $this->ingredient_id = intval($this->ingredient_id);
      $this->recipe_id = intval($this->recipe_id);
      $this->unit_id = (!is_null($this->unit_id) ? intval($this->unit_id) : null);
      $this->ingredient_quantity = (!is_null($this->ingredient_quantity) ? floatval($this->ingredient_quantity) : null);
    }
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->ingredient_description;
  }

  public function getId() : int {
    return $this->ingredient_id;
  }

  public function getQuantity() : ?float {
    return $this->ingredient_quantity;
  }

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipe_id);
  }

  public function getRecipeId() : int {
    return $this->recipe_id;
  }

  public function getUnit() : ?UnitInterface {
    global $Controller;
    return !is_null($this->unit_id) ? $Controller->OM()->Unit($this->unit_id) : null;
  }

  public function getUnitId() : ?int {
    return $this->unit_id;
  }

  public function jsonSerialize() {
    return [
      'id' => $this->ingredient_id,
      'unitId' => $this->unit_id,
      'unit' => (!is_null($this->unit_id) ? $this->getUnit() : ['id' => 0, 'name' => '']),
      'quantity' => $this->ingredient_quantity,
      'quantityCalc' => $this->ingredient_quantity,
      'description' => $this->ingredient_description,
    ];
  }

}
