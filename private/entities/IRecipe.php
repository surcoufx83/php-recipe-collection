<?php

namespace Surcouf\PhpArchive;

use \DateTime;

if (!defined('CORE2'))
  exit;

interface IRecipe {

  public function addIngredients(array $record) : void;
  public function getCookedCount() : int;
  public function getCookedCountStr() : string;
  public function getCreationDate() : DateTime;
  public function getCreationDateStr() : string;
  public function getDescription() : string;
  public function getEaterCount() : int;
  public function getEaterCountStr() : string;
  public function getId() : int;
  public function getIngredients() : array;
  public function getName() : string;
  public function getPictures() : array;
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
  public function getTags() : array;
  public function getTagVotes() : array;
  public function getUser() : ?User;
  public function getUserId() : ?int;
  public function getVotedCount() : int;
  public function getVotedCountStr() : string;
  public function getVoting() : ?float;
  public function getVotingStr() : string;
  public function isPublished() : bool;
  public function loadComplete() : void;

}
