<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;
use BenMajor\ImageResize\Image;

if (!defined('CORE2'))
  exit;

class Picture implements PictureInterface, DbObjectInterface, HashableInterface {

  protected $picture_id,
            $recipe_id,
            $user_id,
            $picture_sortindex,
            $picture_name,
            $picture_description,
            $picture_hash,
            $picture_filename,
            $picture_full_path;
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
    } else {
      $this->picture_id = intval($this->picture_id);
      $this->recipe_id = intval($this->recipe_id);
      $this->user_id = !is_null($this->user_id) ? intval($this->user_id) : null;
      $this->picture_sortindex = intval($this->picture_sortindex);
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

  private function cropImage(?int $width=null, ?int $height=null) : string {
    $sizestr = sprintf('%dx%d', $width ?? 0, $height ?? 0);
    $filename =  $this->picture_hash.$this->picture_id.$sizestr.'.'.$this->getExtension();
    $path = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, 'cbimages', $filename);
    if (FilesystemHelper::file_exists($path))
      return $filename;
    $copyfile = copy($this->picture_full_path, $path);
    $img = new Image($path);
    $img->disableRename();
    if (!is_null($width) && !is_null($height))
      $img->resizeCrop($width, $height);
    else if (!is_null($width))
      $img->resizeCrop($width);
    else
      $img->resizeCrop($height);
    $img->output(FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, 'cbimages'));
    return $filename;

    var_dump($sizestr);
    var_dump($width, $height);
    var_dump($img);
    exit;
    return '';
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

  public function getFilename(?int $width=null, ?int $height=null) : string {
    if (is_null($height) && is_null($width))
      return $this->picture_filename;
    return $this->cropImage($width, $height);
  }

  public function getFullpath() : string {
    return $this->picture_full_path;
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

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipe_id);
  }

  public function getRecipeId() : int {
    return $this->recipe_id;
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

}
