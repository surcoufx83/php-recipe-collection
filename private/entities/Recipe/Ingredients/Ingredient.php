<?php

namespace Surcouf\Cookbook\Recipe\Ingredients;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;

if (!defined('CORE2'))
  exit;

class Ingredient implements IngredientInterface, DbObjectInterface {

  protected $id, $recipeid, $unitid, $quantity, $description;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['ingredient_id']);
    $this->recipeid = intval($dr['recipe_id']);
    $this->unitid = intval($dr['unit_id']);
    $this->quantity = (!is_null($dr['ingredient_quantity']) ? floatval($dr['ingredient_quantity']) : null);
    $this->description = $dr['ingredient_description'];
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

  public function getRecipe() : ?RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipeid);
  }

  public function getRecipeId() : ?int {
    return $this->recipeid;
  }

  public function getUnit() : ?UnitInterface {
    global $Controller;
    return $Controller->getUnit($this->unitid);
  }

  public function getUnitId() : ?int {
    return $this->unitid;
  }

}
