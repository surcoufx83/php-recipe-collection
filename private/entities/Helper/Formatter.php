<?php

namespace Surcouf\Cookbook\Helper;

use \DateTime;

if (!defined('CORE2'))
  exit;

final class Formatter implements FormatterInterface {

  public static $ByteSymbols = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

  public const FOValuePrefix = 1;
  public const FOValueAppendix = 2;

  public static function byte_format(int $value, int $precission = -1) : string {
    $exp = $value ? floor(log($value) / log(1024)) : 0;
    $floatval = ($value/pow(1024, floor($exp)));
    return Formatter::float_format($floatval, ($precission > -1 ? $precission : (Formatter::$ByteSymbols[$exp] == 'B' ? 0 : 1))).' '.Formatter::$ByteSymbols[$exp];
  }

  public static function date_format(?DateTime $dt = null, ?string $format = null) : string {
    global $Controller;
    $dt = $dt ?? new DateTime();
    return $dt->format(is_null($format) ? $Controller->Config()->DefaultDateFormatUi() : $format);
  }

  public static function float_format(float $value, int $precission = -1) : string {
    global $Controller;
    $decplcs = $Controller->Config()->DefaultDecimalsCount();
    $decsep = $Controller->Config()->DefaultDecimalsSeparator();
    $thsdsep = $Controller->Config()->DefaultThousandsSeparator();
    return number_format($value, $precission > -1 ? $precission : $decplcs, $decsep, $thsdsep);
  }

  public static function int_format(int $value) : string {
    global $Controller;
    $thsdsep = $Controller->Config()->DefaultThousandsSeparator();
    return number_format($value, 0, '', $thsdsep);
  }

  public static function min_format(int $minutes) : string {
    global $Controller;
    if ($minutes < 60)
      return $Controller->l('common_duration_minutes', $minutes);
    if ($minutes < 1440) {
      $hrs = floor(floatval($minutes) / 60.0 * 2.0) / 2.0;
      $hrs = Formatter::float_format($hrs, $hrs - floor($hrs) == 0 ? 0 : 1);
      return $Controller->l('common_duration_hours', $hrs);
    }
    $days = floor(floatval($minutes) / 60.0 / 24.0 * 2.0) / 2.0;
    $days = Formatter::float_format($days, $days - floor($days) == 0 ? 0 : 1);
    return $Controller->l('common_duration_days', $days);
  }

  public static function t(
    int $numericValue,
    string $singular,
    string $plural,
    int $options,
    string $separator = ' ') : string {

    return
      (Flags::has_flag($options, Formatter::FOValuePrefix) ? Formatter::int_format($numericValue).$separator : '').
      ($numericValue == 1 ? $singular : $plural).
      (Flags::has_flag($options, Formatter::FOValueAppendix) ? $separator.Formatter::int_format($numericValue) : '');
  }

}
