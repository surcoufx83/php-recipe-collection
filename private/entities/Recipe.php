<?php

namespace Surcouf\PhpArchive;

use \DateTime;
use Surcouf\PhpArchive\Helper\ConverterHelper;

if (!defined('CORE2'))
  exit;

class Recipe implements IRecipe, IDbObject {

  private $id, $userid, $ispublic, $name, $description, $eater, $sourcedesc, $sourceurl, $created, $published;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['recipe_id']);
    $this->userid = intval($dr['user_id']);
    $this->ispublic = ConverterHelper::to_bool($dr['recipe_public']);
    $this->name = $dr['recipe_name'];
    $this->description = $dr['recipe_description'];
    $this->eater = intval($dr['recipe_eater']);
    $this->sourcedesc = $dr['recipe_source_desc'];
    $this->sourceurl = $dr['recipe_source_url'];
    $this->created = new DateTime($dr['recipe_created']);
    $this->published = (!is_null($dr['recipe_published']) ? new DateTime($dr['recipe_published']) : null);
  }

  public function getCreationDate() : DateTime {
    return $this->created;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getEaterCount() : int {
    return $this->eater;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName() : string {
    return $this->name;
  }

  public function getPublishedDate() : ?DateTime {
    return $this->published;
  }

  public function getSourceDescription() : string {
    return $this->sourcedesc;
  }

  public function getSourceUrl() : string {
    return $this->sourceurl;
  }

  public function getUser() : ?User {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
  }

  public function isPublished() : bool {
    return $this->ispublic;
  }

}
