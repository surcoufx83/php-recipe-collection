<?php

namespace Surcouf\Cookbook\Helper;

use \DateTime;

if (!defined('CORE2'))
  exit;

final class Formatter implements IFormatter {

  public static $ByteSymbols = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

  public const FOValuePrefix = 1;
  public const FOValueAppendix = 2;

  public static function byte_format(int $value, int $precission = -1) : string {
    $exp = $value ? floor(log($value) / log(1024)) : 0;
    $floatval = ($value/pow(1024, floor($exp)));
    return Formatter::float_format($floatval, ($precission > -1 ? $precission : (Formatter::$ByteSymbols[$exp] == 'B' ? 0 : 1))).' '.Formatter::$ByteSymbols[$exp];
  }

  public static function date_format(?DateTime $dt = null, ?string $format = null) : string {
    global $Config;
    $dt = $dt ?? new DateTime();
    if (!is_null($Config) && is_null($format)) {
      return $dt->format($Config->DateFormat->UiFormat->getString());
    }
    return $dt->format($format ?? 'd. F Y');
  }

  public static function float_format(float $value, int $precission = -1) : string {
    global $Config;
    if (!is_null($Config)) {
      $decplcs = $Config->Defaults->NumberFormat->Decimals->getInt();
      $decsep = $Config->Defaults->NumberFormat->DecimalsSeparator->getString();
      $thsdsep = $Config->Defaults->NumberFormat->ThousandsSeparator->getString();
    } else {
      $decplcs = 2;
      $decsep = '.';
      $thsdsep = '';
    }
    return number_format($value, $precission > -1 ? $precission : $decplcs, $decsep, $thsdsep);
  }

  public static function int_format(int $value) : string {
    global $Config;
    if (!is_null($Config)) {
      $thsdsep = $Config->Defaults->NumberFormat->ThousandsSeparator->getString();
    } else {
      $thsdsep = '';
    }
    return number_format($value, 0, '', $thsdsep);
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
