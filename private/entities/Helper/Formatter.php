<?php

namespace Surcouf\Cookbook\Helper;

use \DateTime;

if (!defined('CORE2'))
  exit;

final class Formatter implements FormatterInterface {

  public static $ByteSymbols = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

  public const FOValuePrefix = 1;
  public const FOValueAppendix = 2;

  /**
   * Formats a byte size specification into a readable string.
   *
   * @param int $value      Specification of the bytes (e.g. file size).
   * @param int $precission Number of decimal places. Optional, default = -1 = system default.
   * @return string         The file size string with symbol.
   */
  public static function byte_format(int $value, int $precission = -1) : string {
    $exp = $value ? floor(log($value) / log(1024)) : 0;
    $floatval = ($value/pow(1024, floor($exp)));
    return Formatter::float_format($floatval, ($precission > -1 ? $precission : (Formatter::$ByteSymbols[$exp] == 'B' ? 0 : 1))).' '.Formatter::$ByteSymbols[$exp];
  }

  /**
   * Formats a DateTime object.
   *
   * @param DateTime $dt   The DateTime object to be output as a formatted string. Optional, Default = null = Current DateTime.
   * @param string $format The format in which the object should be output. Optional, Default = null = UI format according to configuration.
   * @return string         The formatted string.
   */
  public static function date_format(?DateTime $dt = null, ?string $format = null) : string {
    global $Controller;
    $dt = $dt ?? new DateTime();
    return $dt->format(is_null($format) ? $Controller->Config()->Defaults('Formats', 'UiLongDate') : $format);
  }

  /**
   * Formats a float value.
   *
   * @param float $value    The value to be formatted.
   * @param int $precission Number of decimal places. Optional, default = -1 = system default.
   * @return string         The formatted string.
   */
  public static function float_format(float $value, int $precission = -1) : string {
    global $Controller;
    $decplcs = $Controller->Config()->Defaults('Formats', 'Decimals');
    $decsep = $Controller->Config()->Defaults('Formats', 'DecimalsSeparator');
    $thsdsep = $Controller->Config()->Defaults('Formats', 'ThousandsSeparator');
    return number_format($value, $precission > -1 ? $precission : $decplcs, $decsep, $thsdsep);
  }

  /**
   * Formats an integer value.
   *
   * @param int $value The value to be formatted.
   * @return string    The formatted string.
   */
  public static function int_format(int $value) : string {
    global $Controller;
    $thsdsep = $Controller->Config()->Defaults('Formats', 'ThousandsSeparator');
    return number_format($value, 0, '', $thsdsep);
  }

  /**
   * Formats an string for an url.
   *
   * @param string $value The value to be formatted.
   * @return string    The formatted string.
   */
  public static function nice_urlstring(string $value) : string {
    return str_replace('/', '-', str_replace(' ', '_', $value));
  }

}
