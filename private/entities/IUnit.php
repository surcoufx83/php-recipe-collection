<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface IUnit {

  public function getId() : int;
  public function getName() : string;

}
