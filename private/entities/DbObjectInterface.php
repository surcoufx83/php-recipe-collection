<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface DbObjectInterface {

  public function getDbChanges() : array;
  public function getId() : int;

}
