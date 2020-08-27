<?php

namespace Surcouf\Cookbook\Helper;

use \DateInterval;
use \DateTime;

if (!defined('CORE2'))
  exit;

interface IDateTimeHelper {

  public static function dateInterval2IsoFormat(DateInterval $interval) : string;

}
