<?php

namespace Surcouf\PhpArchive;

use \DateTime;

if (!defined('CORE2'))
  exit;

interface IRecipe {

  public function getCreationDate() : DateTime;
  public function getDescription() : string;
  public function getEaterCount() : int;
  public function getId() : int;
  public function getName() : string;
  public function getPublishedDate() : ?DateTime;
  public function getSourceDescription() : string;
  public function getSourceUrl() : string;
  public function getUser() : ?User;
  public function getUserId() : ?int;
  public function isPublished() : bool;

}
