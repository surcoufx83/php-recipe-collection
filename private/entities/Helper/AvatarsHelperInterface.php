<?php

namespace Surcouf\Cookbook\Helper;

if (!defined('CORE2'))
  exit;

interface AvatarsHelperInterface {

  public static function createAvatar(string $payload, string $filename) : string;
  public static function exists(string $filename) : bool;

}
