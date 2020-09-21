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

  private function cropImage(string $path, ?int $width=null, ?int $height=null) : string {
    $sizestr = sprintf('%dx%d', $width ?? 0, $height ?? 0);
    $filename =  $this->picture_hash.$this->recipe_id.$sizestr.'.'.$this->getExtension();
    $path = FilesystemHelper::paths_combine($path, $filename);
    if (FilesystemHelper::file_exists($path))
      return $filename;
    $copyfile = copy($this->path, $path);
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
  }

  public function moveTo(string $path, int $recipeId) : bool {
    $this->recipe_id = $recipeId;
    try {
      $this->picture_filename = $this->cropImage($path, 1920, 1080);
      $this->picture_full_path = FilesystemHelper::paths_combine($path, $this->picture_filename);
      $this->cropImage($path, 60);
    } catch(Exception $e) {
      return false;
    }
    return true;
  }

  public function setId(int $newId) : PictureInterface {
    $this->picture_id = $newId;
    return $this;
  }

}
