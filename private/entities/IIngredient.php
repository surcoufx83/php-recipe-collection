<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

interface IIngredient {

  public function getId() : int;
  public function getName(float $amount = 1.0, string $lang = null) : string;

}
