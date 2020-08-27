<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface IDbObject {

  public function getDbChanges() : array;
  public function getId() : int;

}
