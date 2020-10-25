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
   * Formats an integer specifying a number of minutes.
   *
   * @param int $value The value to be formatted.
   * @param Boolean $useFractals default: true, True if 1/2 instead of .5
   * @return string    The formatted string.
   */
  public static function min_format(int $minutes, bool $useFractals = true) : string {
    global $Controller;
    $minutes = floatval($minutes);
    if ($minutes < 30.0){
      if ($minutes == 1)
        return $Controller->l('common_duration_minute', $minutes);
      return $Controller->l('common_duration_minutes', $minutes);
    }
    if ($minutes < 720.0) {
      $halfhour = ceil($minutes / 60.0 * 2.0) / 2.0;
      if ($useFractals) {
        if ($halfhour == .5)
          return $Controller->l('common_duration_hour', '&frac12;');
        if ($halfhour == 1.0)
          return $Controller->l('common_duration_hour', $halfhour);
        if (floor($halfhour) != $halfhour)
          return $Controller->l('common_duration_hours', floor($halfhour).'&frac12;');
        return $Controller->l('common_duration_hours', floor($halfhour));
      }
      if ($halfhour == 1.0)
        return $Controller->l('common_duration_hour', Formatter::float_format($halfhour, $halfhour - floor($halfhour) == 0 ? 0 : 1));
      return $Controller->l('common_duration_hours', Formatter::float_format($halfhour, $halfhour - floor($halfhour) == 0 ? 0 : 1));
    }
    $halfdays = ceil($minutes / 1440.0 * 2.0) / 2.0;
    if ($useFractals) {
      if ($halfdays == .5)
        return $Controller->l('common_duration_day', '&frac12;');
      if ($halfdays == 1.0)
        return $Controller->l('common_duration_day', $halfdays);
      if (floor($halfdays) != $halfdays)
        return $Controller->l('common_duration_days', floor($halfdays).'&frac12;');
      return $Controller->l('common_duration_days', floor($halfdays));
    }
    if ($halfdays == 1.0)
      return $Controller->l('common_duration_day', Formatter::float_format($halfdays, $halfdays - floor($halfdays) == 0 ? 0 : 1));
    return $Controller->l('common_duration_days', Formatter::float_format($halfdays, $halfdays - floor($halfdays) == 0 ? 0 : 1));
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

  /**
   * Selects the singular or plural form depending on the numeric value and outputs it.
   * The numeric value can be optionally set in front or behind.
   *
   * @param int $numericValue The numerical value.
   * @param string $singular  The singular string.
   * @param string $plural    The plural string.
   * @param int $options      Options to set the value in front or behind. Optional, default = 0 = no display. Possible values: Formatter::FOValuePrefix, Formatter::FOValueAppendix or both.
   * @param string $separator The separator between value and string. Optional, default = ' '.
   * @return string           The formatted string.
   */
  public static function t(
    int $numericValue,
    string $singular,
    string $plural,
    int $options = 0,
    string $separator = ' ') : string {
    return
      (Flags::has_flag($options, Formatter::FOValuePrefix) ? Formatter::int_format($numericValue).$separator : '').
      ($numericValue == 1 ? $singular : $plural).
      (Flags::has_flag($options, Formatter::FOValueAppendix) ? $separator.Formatter::int_format($numericValue) : '');
  }

}
