<?php

namespace Surcouf\Cookbook\Recipe\Social\Tags;

use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class Tag implements TagInterface, DbObjectInterface {

  protected $id, $name;
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
