<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

interface IPicture {

  public function getDescription() : string;
  public function getFilename() : string;
  public function getHash(bool $calculateIfNull = true) : ?string;
  public function getId() : int;
  public function getIndex() : int;
  public function getName() : string;
  public function getRecipe() : ?Recipe;
  public function getRecipeId() : ?int;
  public function getUser() : ?User;
  public function getUserId() : ?int;

}