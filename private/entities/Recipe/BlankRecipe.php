<?php

namespace Surcouf\Cookbook\Recipe;

use \DateTime;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;

if (!defined('CORE2'))
  exit;

class BlankRecipe extends Recipe implements RecipeInterface, DbObjectInterface {

  public function __construct() {
    global $Controller;
    $this->user_id = $Controller->User()->getId();
    $this->recipe_public = false;
    $this->recipe_created = new DateTime();
    $this->recipe_published = null;
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
    $this->recipe_description = $newDescription;
    return $this;
  }

  public function setEaterCount(int $newCount) : RecipeInterface {
    $this->recipe_eater = $newCount;
    return $this;
  }

  public function setId(int $newId) : RecipeInterface {
    $this->recipe_id = $newId;
    return $this;
  }

  public function setName(string $newName) : RecipeInterface {
    $this->recipe_name = $newName;
    return $this;
  }

  public function setSourceDescription(string $newDescription) : RecipeInterface {
    $this->recipe_source_desc = $newDescription;
    return $this;
  }

  public function setSourceUrl(string $newUrl) : RecipeInterface {
    $this->recipe_source_url = $newUrl;
    return $this;
  }

}
