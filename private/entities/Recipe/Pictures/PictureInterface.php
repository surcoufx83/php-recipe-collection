<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use DateTime;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

interface PictureInterface {

  public function getDescription() : string;
  public function getExtension() : string;
  public function getFilename(bool $thumbnail = false) : string;
  public function getFolderName() : string;
  public function getFullpath(bool $thumbnail = false) : string;
  public function getId() : int;
  public function getIndex() : int;
  public function getName() : string;
  public function getPublicPath(bool $thumbnail = false) : string;
  public function getRecipe() : RecipeInterface;
  public function getRecipeId() : int;
  public function getUploadDate() : DateTime;
  public function getUser() : ?UserInterface;
  public function getUserId() : ?int;

}
