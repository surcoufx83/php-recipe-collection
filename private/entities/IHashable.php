<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface IHashable {

  public function calculateHash() : string;
  public function getHash(bool $createIfNull = true) : ?string;
  public function hasHash() : bool;

}
