<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

interface IUnit {
  public function getName(string $lang, bool $plural = false) : string;
}
