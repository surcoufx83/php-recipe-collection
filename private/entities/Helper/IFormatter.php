<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

interface IFormatter {

  public static function byte_format(int $value, int $precission = -1) : string;
  public static function float_format(float $value, int $precission = -1) : string;
  public static function int_format(int $value) : string;
  public static function t(int $numericValue, string $singular, string $plural, int $options, string $separator = ' ') : string;

}
