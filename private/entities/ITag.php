<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

interface ITag {

  public function getId() : int;
  public function getName() : string;

}
