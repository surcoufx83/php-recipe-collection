<?php

namespace Surcouf\Cookbook\Helper;

use \DateTime;

if (!defined('CORE2'))
  exit;

interface FormatterInterface {

  public static function byte_format(int $value, int $precission = -1) : string;
  public static function date_format(?DateTime $dt = null, ?string $format = null) : string;
  public static function float_format(float $value, int $precission = -1) : string;
  public static function int_format(int $value) : string;

}
