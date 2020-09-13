<?php

namespace Surcouf\Cookbook\Helper;

if (!defined('CORE2'))
  exit;

interface ConverterHelperInterface {

  public static function bool_to_str(bool $b) : string;

  public static function to_bool($value) : bool;

}
