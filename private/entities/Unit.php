<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

class Unit implements IUnit, IDbObject {

  private $id, $decimals, $namedesn, $namedepl, $nameensn, $nameenpl;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['unit_id']);
    $this->decimals = intval($dr['unit_decimals']);
    $this->namedesn = $dr['unit_name_de_sn'];
    $this->namedepl = $dr['unit_name_de_pl'];
    $this->nameensn = $dr['unit_name_en_sn'];
    $this->nameenpl = $dr['unit_name_en_pl'];
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDecimals() : int {
    return $this->decimals;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName(string $lang = null, float $amount = 1.0) : string {
    if (is_null($lang)) {
      global $Controller;
      $lang = $Controller->Language();
    }
    if ($lang == 'de')
      return ($amount == 1.0 ? $this->namedesn : $this->namedepl);
    return ($amount == 1.0 ? $this->nameensn : $this->nameenpl);
  }

}
