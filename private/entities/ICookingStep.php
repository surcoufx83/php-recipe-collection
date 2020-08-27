<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface ICookingStep {

  public function getContent() : string;
  public function getChillTime() : int;
  public function getCookingTime() : int;
  public function getId() : int;
  public function getIndex() : int;
  public function getPreparationTime() : int;
  public function getRecipe() : ?Recipe;
  public function getRecipeId() : ?int;
  public function getStepNo() : int;
  public function getTitle() : string;

}
