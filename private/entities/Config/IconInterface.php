<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

interface IconInterface {

  public function getIcon() : string;
  public function getSpace() : string;

}
