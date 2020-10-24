<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;
use BenMajor\ImageResize\Image;

if (!defined('CORE2'))
  exit;

class BlankPicture extends Picture implements HashableInterface {

  public function __construct(int $index, string $name, string $path) {
    global $Controller;
    $this->picture_sortindex = $index;
    $this->picture_name = $name;
    $this->picture_hash = $this->calculateHash();
    $this->picture_filename = $name;
    $this->path = $path;
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->picture_name,
      $this->picture_filename,
    ];
    $this->picture_hash = HashHelper::hash(join($data));
    return $this->picture_hash;
  }

  public function getFolderName() : string {
    return substr($this->picture_filename, 0, 2);
  }

  public function moveTo(int $recipeId) : bool {
    $this->recipe_id = $recipeId;
    $this->picture_filename = $this->picture_hash.$this->recipe_id.'.'.$this->getExtension();
    $folder = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, 'cbimages', $this->getFolderName());
    if (!is_dir($folder))
      mkdir($folder, 0644, true);
    $this->picture_full_path = FilesystemHelper::paths_combine($folder, $this->picture_filename);
    return move_uploaded_file($this->path, $this->picture_full_path);
  }

  public function setId(int $newId) : PictureInterface {
    $this->picture_id = $newId;
    return $this;
  }

}
