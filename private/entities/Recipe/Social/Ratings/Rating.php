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

  protected $entry_id,
            $user_id,
            $recipe_id,
            $entry_datetime,
            $entry_comment,
            $entry_viewed,
            $entry_cooked,
            $entry_vote,
            $entry_rate;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->entry_id = intval($record['entry_id']);
      $this->user_id = intval($record['user_id']);
      $this->recipe_id = intval($record['recipe_id']);
      $this->entry_datetime = new DateTime($record['entry_datetime']);
      $this->entry_comment = $record['entry_comment'];
      $this->entry_viewed = (!is_null($record['entry_viewed']) ? ConverterHelper::to_bool($record['entry_viewed']) : null);
      $this->entry_cooked = (!is_null($record['entry_cooked']) ? ConverterHelper::to_bool($record['entry_cooked']) : null);
      $this->entry_vote = (!is_null($record['entry_vote']) ? intval($record['entry_vote']) : null);
      $this->entry_rate = (!is_null($record['entry_rate']) ? intval($record['entry_rate']) : null);
    } else {
      $this->entry_id = intval($this->entry_id);
      $this->user_id = intval($this->user_id);
      $this->recipe_id = intval($this->recipe_id);
      $this->entry_datetime = new DateTime($this->entry_datetime);
      $this->entry_viewed = (!is_null($this->entry_viewed) ? ConverterHelper::to_bool($this->entry_viewed) : null);
      $this->entry_cooked = (!is_null($this->entry_cooked) ? ConverterHelper::to_bool($this->entry_cooked) : null);
      $this->entry_vote = (!is_null($this->entry_vote) ? intval($this->entry_vote) : null);
      $this->entry_rate = (!is_null($this->entry_rate) ? intval($this->entry_rate) : null);
    }
  }

  public function getComment() : string {
    return $this->entry_comment;
  }

  public function getDate() : DateTime {
    return $this->entry_datetime;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->entry_id;
  }

  public function getRating() : int {
    return $this->entry_rate;
  }

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipe_id);
  }

  public function getRecipeId() : int {
    return $this->recipe_id;
  }

  public function getUser() : UserInterface {
    global $Controller;
    return $Controller->getUser($this->user_id);
  }

  public function getUserId() : int {
    return $this->user_id;
  }

  public function getVoting() : int {
    return $this->entry_vote;
  }

  public function hasCooked() : bool {
    return ($this->entry_cooked == true);
  }

  public function hasRated() : bool {
    return !is_null($this->entry_rate);
  }

  public function hasViewed() : bool {
    return ($this->entry_viewed == true);
  }

  public function hasVoted() : bool {
    return !is_null($this->entry_vote);
  }

}
