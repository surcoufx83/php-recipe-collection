<?php

namespace Surcouf\Cookbook\Recipe\Social\Ratings;

use \DateTime;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class Rating implements RatingInterface, DbObjectInterface {

  protected $id, $recipeid, $userid, $comment, $date, $viewed, $cooked, $voting, $rating;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['entry_id']);
    $this->recipeid = intval($dr['recipe_id']);
    $this->userid = intval($dr['user_id']);
    $this->comment = $dr['entry_comment'];
    $this->date = $dr['entry_datetime'];
    $this->viewed = (!is_null($dr['entry_viewed']) ? ConverterHelper::to_bool($dr['entry_viewed']) : null);
    $this->cooked = (!is_null($dr['entry_cooked']) ? ConverterHelper::to_bool($dr['entry_cooked']) : null);
    $this->voting = (!is_null($dr['entry_vote']) ? intval($dr['entry_vote']) : null);
    $this->rating = (!is_null($dr['entry_rate']) ? intval($dr['entry_rate']) : null);
  }

  public function getComment() : string {
    return $this->comment;
  }

  public function getDate() : DateTime {
    return $this->date;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getRating() : int {
    return $this->rating;
  }

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->getRecipe($this->recipeid);
  }

  public function getRecipeId() : int {
    return $this->recipeid;
  }

  public function getUser() : ?UserInterface {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
  }

  public function getVoting() : int {
    return $this->voting;
  }

  public function hasCooked() : bool {
    return ($this->cooked == true);
  }

  public function hasRated() : bool {
    return !is_null($this->rating);
  }

  public function hasViewed() : bool {
    return ($this->viewed == true);
  }

  public function hasVoted() : bool {
    return !is_null($this->voting);
  }

}
