<?php

namespace Surcouf\Cookbook\Recipe\Pictures;

use DateTime;
use Imagick;
use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\User\UserInterface;

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

  public function createThumbnail() : bool {
    global $Controller;
    $img = new Imagick();
    $raw = \file_get_contents($this->getFullpath());
    $img->readImageBlob($raw);
    $orientation = $img->getImageOrientation();
    switch($orientation) {
      case imagick::ORIENTATION_BOTTOMRIGHT:
        $img->rotateimage("#000", 180); // rotate 180 degrees
        break;
      case imagick::ORIENTATION_RIGHTTOP:
        $img->rotateimage("#000", 90); // rotate 90 degrees CW
        break;
      case imagick::ORIENTATION_LEFTBOTTOM:
        $img->rotateimage("#000", -90); // rotate 90 degrees CCW
        break;
    }
    $img->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
    $width = $img->getImageWidth();
    $height = $img->getImageHeight();
    $img->setImageCompressionQuality(85);
    if ($Controller->Config()->System('Thumbnails', 'Resize') === true)
      $img->thumbnailImage($Controller->Config()->System('Thumbnails', 'Width'), $Controller->Config()->System('Thumbnails', 'Height'), true);
    else
      $img->thumbnailImage($width, $height);
    $img->setImageFormat('jpeg');
    $img->setSamplingFactors(['2x2', '1x1', '1x1']);
    $profiles = $img->getImageProfiles('icc', true);
    $img->stripImage();
    if (!empty($profiles))
      $img->profileImage('icc', $profiles['icc']);
    $img->setInterlaceScheme(Imagick::INTERLACE_JPEG);
    $img->setColorspace(Imagick::COLORSPACE_SRGB);
    $img->writeImage($this->getFullpath(true));
    $img->destroy();
    return true;
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

  public function getFilename(bool $thumbnail = false) : string {
    return $filename = sprintf('%s%s.%s', pathinfo($this->picture_filename, PATHINFO_FILENAME), ($thumbnail ? '-thb' : ''), ($thumbnail ? 'jpg' : $this->getExtension()));
  }

  public function getFolderName() : string {
    return substr($this->picture_filename, 0, 2);
  }

  public function getFullpath(bool $thumbnail = false) : string {
    return FilesystemHelper::paths_combine(pathinfo($this->picture_full_path, PATHINFO_DIRNAME), $this->getFilename($thumbnail));
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

  public function getPublicPath(bool $thumbnail = false) : string {
    return '/pictures/cbimages/'.$this->getFolderName().'/'.$this->getFilename($thumbnail);
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
      'link' => '/images/'.$this->recipe_id.'/'.$this->picture_id.'/raw',
      'thumbnail' => '/images/'.$this->recipe_id.'/'.$this->picture_id,
      'name' => $this->picture_name,
      'uploaded' => $this->picture_uploaded->format(DateTime::ISO8601),
      'uploadFile' => null,
      'uploaderId' => (!is_null($this->user_id) ? $this->user_id : 0),
      'uploaderName' => (!is_null($this->user_id) ? $this->getUser()->getUsername() : ''),
    ];
  }

}
