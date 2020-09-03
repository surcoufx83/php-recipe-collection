<?php

namespace Surcouf\Cookbook\Recipe\Ingredients;

use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;

if (!defined('CORE2'))
  exit;

interface IngredientInterface {

  public function getDescription() : string;
  public function getId() : int;
  public function getQuantity() : ?float;
  public function getRecipe() : ?RecipeInterface;
  public function getRecipeId() : ?int;
  public function getUnit() : ?UnitInterface;
  public function getUnitId() : ?int;

}
