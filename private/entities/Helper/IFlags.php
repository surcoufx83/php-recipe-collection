<?php

namespace Surcouf\Cookbook\Helper;

if (!defined('CORE2'))
  exit;

interface IFlags {

  public static function add_flag(int &$value, int $flag) : void;
  public static function has_flag(int $value, int $flag) : bool;
  public static function remove_flag(int &$value, int $flag) : void;

}
