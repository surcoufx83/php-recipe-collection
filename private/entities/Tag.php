<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

class Tag implements ITag, IDbObject {

  private $id, $name;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['tag_id']);
    $this->name = $dr['tag_name'];
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName() : string {
    return $this->name;
  }

}
