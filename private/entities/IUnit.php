<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

interface IUnit {

  public function getDecimals() : int;
  public function getId() : int;
  public function getName(string $lang = null, float $amount = 1.0) : string;

}
