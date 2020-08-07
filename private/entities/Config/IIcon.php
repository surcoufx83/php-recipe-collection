<?php

namespace Surcouf\PhpArchive\Config;

if (!defined('CORE2'))
  exit;

interface IIcon {

  public function getIcon(?string $cssClass=null, ?string $customStyle=null, ?string $id=null) : string;
  public function getDataArray() : array;

}
