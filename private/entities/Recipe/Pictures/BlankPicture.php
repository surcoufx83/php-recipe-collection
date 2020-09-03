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

  public function getExtension() : string {
    return pathinfo($this->filename, PATHINFO_EXTENSION);
  }

  public function moveTo(string $filesystemLocation) : bool {
    if (file_exists($filesystemLocation))
      return false;
    if (!move_uploaded_file($this->path, $filesystemLocation))
      return false;
    $this->location = $filesystemLocation;
    return true;
  }

  public function setFilename(string $newName) : PictureInterface {
    $this->filename = $newName;
    return $this;
  }

  public function setId(int $newId) : PictureInterface {
    $this->id = $newId;
    return $this;
  }

}
