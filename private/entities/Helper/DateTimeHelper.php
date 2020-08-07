<?php

namespace Surcouf\PhpArchive\Helper;

use \DateInterval;
use \DateTime;

if (!defined('CORE2'))
  exit;

final class DateTimeHelper implements IDateTimeHelper {

  public static function dateInterval2IsoFormat(DateInterval $interval) : string {
    // https://stackoverflow.com/questions/33787039/format-dateinterval-as-iso8601
    list($date,$time) = explode("T",$interval->format("P%yY%mM%dDT%hH%iM%sS"));
    // now, we need to remove anything that is a zero, but make sure to not remove
    // something like 10D or 20D
    $res =
        str_replace([ 'M0D', 'Y0M', 'P0Y' ], [ 'M', 'Y', 'P' ], $date) .
        rtrim(str_replace([ 'M0S', 'H0M', 'T0H'], [ 'M', 'H', 'T' ], "T$time"),"T");
    if ($res == 'P') // edge case - if we remove everything, DateInterval will hate us later
        return 'PT0S';
    return $res;
  }

}
