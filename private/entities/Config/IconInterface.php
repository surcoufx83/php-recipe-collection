<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

interface IconInterface {

  public function getIcon(?string $cssClass=null, ?string $customStyle=null, ?string $id=null) : string;
  public function getDataArray() : array;

}