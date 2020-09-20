<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;

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

  public function moveTo(string $filesystemLocation) : bool {
    if (file_exists($filesystemLocation))
      return false;
    if (!move_uploaded_file($this->path, $filesystemLocation))
      return false;
    $this->picture_full_path = $filesystemLocation;
    return true;
  }

  public function setFilename(string $newName) : PictureInterface {
    $this->picture_filename = $newName;
    return $this;
  }

  public function setId(int $newId) : PictureInterface {
    $this->picture_id = $newId;
    return $this;
  }

}
