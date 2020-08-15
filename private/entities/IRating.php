<?php

namespace Surcouf\PhpArchive;

use \DateTime;

if (!defined('CORE2'))
  exit;

interface IRating {

  public function getComment() : string;
  public function getCookedDate() : ?DateTime;
  public function getId() : int;
  public function getRatedDate() : ?DateTime;
  public function getRating() : int;
  public function getRecipe() : ?Recipe;
  public function getUser() : ?User;
  public function getUserId() : ?int;
  public function getVotedDate() : ?DateTime;
  public function getVoting() : int;
  public function hasCooked() : bool;
  public function hasVoted() : bool;
  public function hasRated() : bool;

}
