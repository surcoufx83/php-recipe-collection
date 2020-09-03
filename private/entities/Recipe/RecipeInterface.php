<?php

namespace Surcouf\Cookbook\Recipe;

use \DateTime;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

interface RecipeInterface {

  public function addIngredients(IngredientInterface &$ingredient) : void;
  public function addPicture(PictureInterface &$picture) : void;
  public function addRating(RatingInterface &$rating) : void;
  public function addStep(CookingStepInterface &$step) : void;
  public function addTag(TagInterface &$tag, int $votes) : void;
  public function getCookedCount() : int;
  public function getCookedCountStr() : string;
  public function getCreationDate() : DateTime;
  public function getCreationDateStr() : string;
  public function getDescription() : string;
  public function getEaterCount() : int;
  public function getEaterCountStr() : string;
  public function getId() : int;
  public function getIngredients() : array;
  public function getIngredientsCount() : int;
  public function getName() : string;
  public function getPictures() : array;
  public function getPictureCount() : int;
  public function getPublishedDate() : ?DateTime;
  public function getPublishedDateStr() : string;
  public function getRatedCount() : int;
  public function getRatedCountStr() : string;
  public function getRating() : ?float;
  public function getRatingStr() : string;
  public function getRatings() : array;
  public function getSourceDescription() : string;
  public function getSourceUrl() : string;
  public function getSteps() : array;
  public function getStepsCount() : int;
  public function getTags() : array;
  public function getTagsCount() : int;
  public function getTagVotes() : array;
  public function getUser() : ?UserInterface;
  public function getUserId() : ?int;
  public function getViewedCount() : int;
  public function getViewedCountStr() : string;
  public function getVotedCount() : int;
  public function getVotedCountStr() : string;
  public function getVoting() : ?float;
  public function getVotingStr() : string;
  public function isPublished() : bool;
  public function loadComplete() : void;
  public function setDescription(string $newDescription) : RecipeInterface;
  public function setEaterCount(int $newCount) : RecipeInterface;
  public function setName(string $newName) : RecipeInterface;
  public function setPublic(bool $newValue) : RecipeInterface;
  public function setSourceDescription(string $newDescription) : RecipeInterface;
  public function setSourceUrl(string $newUrl) : RecipeInterface;

}
