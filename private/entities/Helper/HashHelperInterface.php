<?php

namespace Surcouf\Cookbook\Helper;

if (!defined('CORE2'))
  exit;

interface HashHelperInterface {

  public static function generate_token(int $length = 32) : ?string;
  public static function getChecksumAlgo() : string;
  public static function getHashAlgo() : string;
  public static function hash(string $input, ?string $algorithm = null) : string;

}
