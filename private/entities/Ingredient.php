<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

class Ingredient implements IIngredient, IDbObject {

  private $id, $namedesn, $namedepl, $nameensn, $nameenpl;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['ing_id']);
    $this->namedesn = $dr['ing_name_de_sn'];
    $this->namedepl = $dr['ing_name_de_pl'];
    $this->nameensn = $dr['ing_name_en_sn'];
    $this->nameenpl = $dr['ing_name_en_pl'];
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getName(float $amount = 1.0, string $lang = null) : string {
    if (is_null($lang)) {
      global $Controller;
      $lang = $Controller->Language();
    }
    if ($lang == 'de')
      return ($amount == 1.0 ? $this->namedesn : $this->namedepl);
    return ($amount == 1.0 ? $this->nameensn : $this->nameenpl);
  }

}
