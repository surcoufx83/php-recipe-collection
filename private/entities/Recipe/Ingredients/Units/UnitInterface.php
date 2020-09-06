<?php

namespace Surcouf\Cookbook\Recipe\Ingredients\Units;

if (!defined('CORE2'))
  exit;

interface UnitInterface {

  public function getId() : int;
  public function getName() : string;
  public function hasId() : bool;

}
