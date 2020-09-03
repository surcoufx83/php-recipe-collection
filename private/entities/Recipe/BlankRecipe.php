<?php

namespace Surcouf\Cookbook\Recipe;

use \DateTime;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;

if (!defined('CORE2'))
  exit;

class BlankRecipe extends Recipe {

  public function __construct() {
    global $Controller;
    $this->userid = $Controller->User()->getId();
    $this->ispublic = false;
    $this->created = new DateTime();
    $this->published = false;
  }

  public function addNewIngredients(IngredientInterface $ingredient) : RecipeInterface {
    $this->ingredients[] = $ingredient;
    return $this;
  }

  public function addNewPicture(PictureInterface $img) : RecipeInterface {
    $this->pictures[] = $img;
    return $this;
  }

  public function addNewStep(CookingStepInterface $step) : RecipeInterface {
    $this->steps[] = $step;
    return $this;
  }

  public function addNewTag(TagInterface $tag) : RecipeInterface {
    $this->tags[] = $tag;
    return $this;
  }

  public function setDescription(string $newDescription) : RecipeInterface {
    $this->description = $newDescription;
    return $this;
  }

  public function setEaterCount(int $newCount) : RecipeInterface {
    $this->eater = $newCount;
    return $this;
  }

  public function setId(int $newId) : RecipeInterface {
    $this->id = $newId;
    return $this;
  }

  public function setName(string $newName) : RecipeInterface {
    $this->name = $newName;
    return $this;
  }

  public function setSourceDescription(string $newDescription) : RecipeInterface {
    $this->sourcedesc = $newDescription;
    return $this;
  }

  public function setSourceUrl(string $newUrl) : RecipeInterface {
    $this->sourceurl = $newUrl;
    return $this;
  }

}
