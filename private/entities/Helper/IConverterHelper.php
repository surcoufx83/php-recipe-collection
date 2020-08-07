<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

interface IConverterHelper {

  public static function bool_to_str(bool $b) : string;

  public static function to_bool($value) : bool;

}
