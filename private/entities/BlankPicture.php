<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Helper\HashHelper;

if (!defined('CORE2'))
  exit;

class BlankPicture implements IPicture, IDbObject, IHashable {

  private $id, $recipeid, $userid, $index, $name, $description, $hash, $filename, $location;
  private $changes = array();

  public function __construct(int $index, string $name, string $path) {
    $this->index = $index;
    $this->name = $name;
    $this->path = $path;
    $this->filename = $name;
    $this->hash = $this->calculateHash();
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->name,
      $this->filename,
    ];
    $this->hash = HashHelper::hash(join($data));
    return $this->hash;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getExtension() : string {
    return pathinfo($this->filename, PATHINFO_EXTENSION);
  }

  public function getFilename() : string {
    return $this->filename;
  }

  public function getFullpath() : string {
    return $this->location;
  }

  public function getHash(bool $calculateIfNull = true) : ?string {
    if (is_null($this->hash))
      $this->calculateHash();
    return $this->hash;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getIndex() : int {
    return $this->index;
  }

  public function getName() : string {
    return $this->name;
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

  public function hasHash() : bool {
    return !is_null($this->hash);
  }

  public function moveTo(string $filesystemLocation) : bool {
    if (file_exists($filesystemLocation))
      return false;
    if (!move_uploaded_file($this->path, $filesystemLocation))
      return false;
    $this->location = $filesystemLocation;
    return true;
  }

  public function setFilename(string $newName) : IPicture {
    $this->filename = $newName;
    return $this;
  }

  public function setId(int $newId) : IPicture {
    $this->id = $newId;
    return $this;
  }

}
