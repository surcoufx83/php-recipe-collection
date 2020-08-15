<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

class Unit implements IUnit, IDbObject {

  private $id, $namedesn, $namedepl, $nameensn, $nameenpl;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['unit_id']);
    $this->namedesn = $dr['unit_name_de_sn'];
    $this->namedepl = $dr['unit_name_de_pl'];
    $this->nameensn = $dr['unit_name_en_sn'];
    $this->nameenpl = $dr['unit_name_en_pl'];
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName(string $lang, bool $plural = false) : string {
    if ($lang == 'de')
      return (!$plural ? $this->namedesn : $this->namedepl);
    return (!$plural ? $this->nameensn : $this->nameenpl);
  }

}
