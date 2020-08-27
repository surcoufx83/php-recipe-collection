<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface IIngredient {

  public function getDescription() : string;
  public function getId() : int;
  public function getQuantity() : ?float;
  public function getRecipe() : ?IRecipe;
  public function getRecipeId() : ?int;
  public function getUnit() : ?IUnit;
  public function getUnitId() : ?int;

}
