<?php

namespace Surcouf\Cookbook\Helper;

if (!defined('CORE2'))
  exit;

final class Flags implements FlagsInterface {

  public static function add_flag(int &$value, int $flag) : void {
    $value |= $flag;
  }

  public static function has_flag(int $value, int $flag) : bool {
    return (($value & $flag) == $flag);
  }

  public static function remove_flag(int &$value, int $flag) : void {
    $value &= ~$flag;
  }

}
