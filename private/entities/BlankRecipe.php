<?php

namespace Surcouf\Cookbook;

use \DateTime;

if (!defined('CORE2'))
  exit;

class BlankRecipe extends Recipe {

  private $id, $userid, $ispublic, $name, $description, $eater, $sourcedesc, $sourceurl, $created, $published;
  private $countcooked = 0, $countvoted = 0, $countrated = 0;
  private $sumvoted = 0, $sumrated = 0;
  private $ingredients = array();
  private $pictures = array();
  private $ratings = array();
  private $steps = array();
  private $tags = array();
  private $tagvotes = array();
  private $changes = array();

  public function __construct() {
    global $Controller;
    $this->userid = $Controller->User()->getId();
    $this->ispublic = false;
    $this->created = new DateTime();
    $this->published = false;
  }

  public function addNewIngredients(IIngredient $ingredient) : IRecipe {
    $this->ingredients[] = $ingredient;
    return $this;
  }

  public function addNewPicture(IPicture $img) : IRecipe {
    $this->pictures[] = $img;
    return $this;
  }

  public function addNewStep(ICookingStep $step) : IRecipe {
    $this->steps[] = $step;
    return $this;
  }

  public function addNewTag(ITag $tag) : IRecipe {
    $this->tags[] = $tag;
    return $this;
  }

  public function getCreationDate() : DateTime {
    return $this->created;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getEaterCount() : int {
    return $this->eater;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getIngredients() : array {
    return $this->ingredients;
  }

  public function getIngredientsCount() : int {
    return count($this->ingredients);
  }

  public function getName() : string {
    return $this->name;
  }

  public function getPictures() : array {
    return $this->pictures;
  }

  public function getPictureCount() : int {
    return count($this->pictures);
  }

  public function getSourceDescription() : string {
    return $this->sourcedesc;
  }

  public function getSourceUrl() : string {
    return $this->sourceurl;
  }

  public function getSteps() : array {
    return $this->steps;
  }

  public function getStepsCount() : int {
    return count($this->steps);
  }

  public function getTags() : array {
    return $this->tags;
  }

  public function getTagsCount() : int {
    return count($this->tags);
  }

  public function getUser() : ?User {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
  }

  public function setDescription(string $newDescription) : IRecipe {
    $this->description = $newDescription;
    return $this;
  }

  public function setEaterCount(int $newCount) : IRecipe {
    $this->eater = $newCount;
    return $this;
  }

  public function setId(int $newId) : IRecipe {
    $this->id = $newId;
    return $this;
  }

  public function setName(string $newName) : IRecipe {
    $this->name = $newName;
    return $this;
  }

  public function setSourceDescription(string $newDescription) : IRecipe {
    $this->sourcedesc = $newDescription;
    return $this;
  }

  public function setSourceUrl(string $newUrl) : IRecipe {
    $this->sourceurl = $newUrl;
    return $this;
  }

}
