<?php

namespace Surcouf\PhpArchive;

use \DateTime;

if (!defined('CORE2'))
  exit;

class BlankRecipe extends Recipe {

  private $id, $userid, $ispublic, $name, $description, $eater, $sourcedesc, $sourceurl, $created, $published;
  private $countcooked = 0, $countvoted = 0, $countrated = 0;
  private $sumvoted = 0, $sumrated = 0;
  private $ingredients = array();
  private $pictures = array();
  private $ratings = array();
  private $steps = array();
  private $tags = array();
  private $tagvotes = array();
  private $changes = array();

  public function __construct() {
    global $Controller;
    $this->userid = $Controller->User()->getId();
    $this->ispublic = false;
    $this->created = new DateTime();
    $this->published = false;
  }

  public function setDescription(string $newDescription) : IRecipe {
    $this->description = $newDescription;
    return $this;
  }

  public function setEaterCount(int $newCount) : IRecipe {
    $this->eater = $newCount;
    return $this;
  }

  public function setName(string $newName) : IRecipe {
    $this->name = $newName;
    return $this;
  }

  public function setSourceDescription(string $newDescription) : IRecipe {
    $this->sourcedesc = $newDescription;
    return $this;
  }

  public function setSourceUrl(string $newUrl) : IRecipe {
    $this->sourceurl = $newUrl;
    return $this;
  }

}
