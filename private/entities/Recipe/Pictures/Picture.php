<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class Picture implements PictureInterface, DbObjectInterface, HashableInterface {

  protected $id, $recipeid, $userid, $index, $name, $description, $hash, $filename, $location;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['picture_id']);
    $this->recipeid = intval($dr['recipe_id']);
    $this->userid = intval($dr['user_id']);
    $this->index = intval($dr['picture_sortindex']);
    $this->name = $dr['picture_name'];
    $this->description = $dr['picture_description'];
    $this->hash = $dr['picture_hash'];
    $this->filename = $dr['picture_filename'];
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->id,
      $this->recipeid,
      $this->name,
      $this->filename,
    ];
    $this->hash = HashHelper::hash(join($data));
    $this->changes['picture_hash'] = $this->hash;
    $Controller->updateDbObject($this);
    return $this->hash;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->description;
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

  public function hasHash() : bool {
    return !is_null($this->hash);
  }

}
