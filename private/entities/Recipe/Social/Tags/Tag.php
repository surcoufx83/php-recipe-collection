<?php

namespace Surcouf\Cookbook\Recipe\Social\Tags;

use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class Tag implements TagInterface, DbObjectInterface {

  protected $tag_id,
            $tag_name;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->tag_id = intval($record['tag_id']);
      $this->tag_name = $record['tag_name'];
    } else {
      $this->tag_id = intval($this->tag_id);
    }
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->tag_id;
  }

  public function getName() : string {
    return $this->tag_name;
  }

}
