<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use DateTime;
use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\User\UserInterface;
use BenMajor\ImageResize\Image;

if (!defined('CORE2'))
  exit;

class Picture implements PictureInterface, DbObjectInterface, HashableInterface, \JsonSerializable {

  protected $picture_id,
            $recipe_id,
            $user_id,
            $picture_sortindex,
            $picture_name,
            $picture_description,
            $picture_hash,
            $picture_filename,
            $picture_full_path,
            $picture_uploaded;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->picture_id = intval($record['picture_id']);
      $this->recipe_id = intval($record['recipe_id']);
      $this->user_id = !is_null($record['user_id']) ? intval($record['user_id']) : null;
      $this->picture_sortindex = intval($record['picture_sortindex']);
      $this->picture_name = $record['picture_name'];
      $this->picture_description = $record['picture_description'];
      $this->picture_hash = $record['picture_hash'];
      $this->picture_filename = $record['picture_filename'];
      $this->picture_full_path = $record['picture_filename'];
      $this->picture_uploaded = new DateTime($record['picture_uploaded']);
    } else {
      $this->picture_id = intval($this->picture_id);
      $this->recipe_id = intval($this->recipe_id);
      $this->user_id = !is_null($this->user_id) ? intval($this->user_id) : null;
      $this->picture_sortindex = intval($this->picture_sortindex);
      $this->picture_uploaded = new DateTime();
    }
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->picture_id,
      $this->recipe_id,
      $this->picture_name,
      $this->picture_filename,
    ];
    $this->picture_hash = HashHelper::hash(join($data));
    $this->changes['picture_hash'] = $this->picture_hash;
    $Controller->updateDbObject($this);
    return $this->picture_hash;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->picture_description;
  }

  public function getExtension() : string {
    return pathinfo($this->picture_filename, PATHINFO_EXTENSION);
  }

  public function getFilename(?int $width=0, ?int $height=0) : string {
    if ($width == 0 && $height == 0)
      return $this->picture_filename;
    return sprintf('%s-%dx%d.jpg', pathinfo($this->picture_filename, PATHINFO_FILENAME), $width, $height);
  }

  public function getFolderName() : string {
    return substr($this->picture_filename, 0, 2);
  }

  public function getFullpath(?int $width=0, ?int $height=0) : string {
    if ($width == 0 && $height == 0)
      return $this->picture_full_path;
    return FilesystemHelper::paths_combine(pathinfo($this->picture_full_path, PATHINFO_DIRNAME), $this->getFilename($width, $height));
  }

  public function getHash(bool $calculateIfNull = true) : ?string {
    if (is_null($this->picture_hash))
      $this->calculateHash();
    return $this->picture_hash;
  }

  public function getId() : int {
    return $this->picture_id;
  }

  public function getIndex() : int {
    return $this->picture_sortindex;
  }

  public function getName() : string {
    return $this->picture_name;
  }

  public function getPublicPath(?int $width=0, ?int $height=0) : string {
    return '/pictures/cbimages/'.$this->getFolderName().'/'.$this->getFilename($width, $height);
  }

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipe_id);
  }

  public function getRecipeId() : int {
    return $this->recipe_id;
  }

  public function getUploadDate() : DateTime {
    return $this->picture_uploaded;
  }

  public function getUser() : ?UserInterface {
    global $Controller;
    return !is_null($this->user_id) ? $Controller->OM()->User($this->user_id) : null;
  }

  public function getUserId() : ?int {
    return $this->user_id;
  }

  public function hasHash() : bool {
    return !is_null($this->picture_hash);
  }

  public function jsonSerialize() {
    global $Controller;
    return [
      'description' => $this->picture_description,
      'id' => $this->picture_id,
      'index' => $this->picture_sortindex,
      'link' => '/images/'.$this->recipe_id.'/'.$this->picture_id,
      'link350' => '/images/350x280/'.$this->recipe_id.'/'.$this->picture_id,
      'link700' => '/images/700x560/'.$this->recipe_id.'/'.$this->picture_id,
      'name' => $this->picture_name,
      'uploaded' => $this->picture_uploaded->format(DateTime::ISO8601),
      'uploadFile' => null,
      'uploaderId' => (!is_null($this->user_id) ? $this->user_id : 0),
      'uploaderName' => (!is_null($this->user_id) ? $this->getUser()->getUsername() : ''),
    ];
  }

}
