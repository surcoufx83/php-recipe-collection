<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

interface IAvatarsHelper {

  public static function createAvatar(string $payload, string $filename) : string;
  public static function exists(string $filename) : bool;

}
