<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface HashableInterface {

  public function calculateHash() : string;
  public function getHash(bool $createIfNull = true) : ?string;
  public function hasHash() : bool;

}
