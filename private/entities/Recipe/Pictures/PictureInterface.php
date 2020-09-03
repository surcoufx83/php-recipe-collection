<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

if (!defined('CORE2'))
  exit;

interface PictureInterface {

  public function getDescription() : string;
  public function getFilename() : string;
  public function getFullpath() : string;
  public function getId() : int;
  public function getIndex() : int;
  public function getName() : string;
  public function getRecipe() : RecipeInterface;
  public function getRecipeId() : int;
  public function getUser() : ?UserInterface;
  public function getUserId() : ?int;

}
