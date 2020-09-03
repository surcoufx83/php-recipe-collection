<?php

namespace Surcouf\Cookbook\Recipe\Social\Ratings;

use \DateTime;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

interface RatingInterface {

  public function getComment() : string;
  public function getCookedDate() : ?DateTime;
  public function getId() : int;
  public function getRatedDate() : ?DateTime;
  public function getRating() : int;
  public function getRecipe() : RecipeInterface;
  public function getRecipeId() : int;
  public function getUser() : ?UserInterface;
  public function getUserId() : ?int;
  public function getVotedDate() : ?DateTime;
  public function getVoting() : int;
  public function hasCooked() : bool;
  public function hasVoted() : bool;
  public function hasRated() : bool;

}
