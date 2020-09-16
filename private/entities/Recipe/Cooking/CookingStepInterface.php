<?php

namespace Surcouf\Cookbook\Recipe\Cooking;

if (!defined('CORE2'))
  exit;

interface CookingStepInterface {

  public function getContent() : string;
  public function getChillTime() : int;
  public function getCookingTime() : int;
  public function getId() : int;
  public function getIndex() : int;
  public function getPreparationTime() : int;
  public function getRecipe() : ?RecipeInterface;
  public function getRecipeId() : ?int;
  public function getStepNo() : int;
  public function getTitle() : string;

}
