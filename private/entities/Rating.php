<?php

namespace Surcouf\PhpArchive;

use \DateTime;

if (!defined('CORE2'))
  exit;

class Rating implements IRating, IDbObject {

  private $id, $recipeid, $userid, $comment;
  private $cookeddate;
  private $voteddate, $voting;
  private $rateddate, $rating;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['entry_id']);
    $this->recipeid = intval($dr['recipe_id']);
    $this->userid = intval($dr['user_id']);
    $this->comment = $dr['entry_comment'];
    $this->cookeddate = (!is_null($dr['entry_cooked']) ? new DateTime($dr['entry_cooked']) : null);
    $this->voteddate = (!is_null($dr['entry_voted']) ? new DateTime($dr['entry_voted']) : null);
    $this->rateddate = (!is_null($dr['entry_rated']) ? new DateTime($dr['entry_rated']) : null);
    $this->voting = (!is_null($dr['vote_value']) ? intval($dr['vote_value']) : -1);
    $this->rating = (!is_null($dr['rating_value']) ? intval($dr['rating_value']) : -1);
  }

  public function getComment() : string {
    return $this->comment;
  }

  public function getCookedDate() : ?DateTime {
    return $this->cookeddate;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getRatedDate() : ?DateTime {
    return $this->rateddate;
  }

  public function getRating() : int {
    return $this->rating;
  }

  public function getRecipe() : ?Recipe {
    global $Controller;
    return $Controller->getRecipe($this->recipeid);
  }

  public function getRecipeId() : ?int {
    return $this->recipeid;
  }

  public function getUser() : ?User {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
  }

  public function getVotedDate() : ?DateTime {
    return $this->voteddate;
  }

  public function getVoting() : int {
    return $this->voting;
  }

  public function hasCooked() : bool {
    return !is_null($this->cookeddate);
  }

  public function hasVoted() : bool {
    return !is_null($this->voteddate);
  }

  public function hasRated() : bool {
    return !is_null($this->rateddate);
  }

}
