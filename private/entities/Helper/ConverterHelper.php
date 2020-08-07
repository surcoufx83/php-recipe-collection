<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

final class ConverterHelper implements IConverterHelper {

  public static function bool_to_str(bool $b) : string {
    return $b === true ? 'true' : 'false';
  }

  public static function to_bool($value) : bool {
    return (
         $value === true
      || $value === 1
      || $value === '1'
      || $value === 'true'
      || $value === 'yes'
    );
  }

}
