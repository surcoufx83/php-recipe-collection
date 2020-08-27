<?php

namespace Surcouf\Cookbook\Helper;

use Surcouf\Cookbook\Controller;

if (!defined('CORE2'))
  exit;

class HashHelper implements IHashHelper {

  public static function generate_token(int $length = 32) : ?string {
      if(!isset($length) || intval($length) <= 8 ){
        $length = 32;
      }
      if (function_exists('random_bytes')) {
          return bin2hex(random_bytes($length));
      }
      if (function_exists('mcrypt_create_iv')) {
          return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
      }
      if (function_exists('openssl_random_pseudo_bytes')) {
          return bin2hex(openssl_random_pseudo_bytes($length));
      }
      return null;
  }

  public static function getHashAlgo() : string {
    global $Controller;
    return $Controller->Config()->ChecksumProvider;
  }

  public static function hash(string $input, ?string $algorithm = null) : string {
    if (!is_null($algorithm))
      return hash($algorithm, $input);
    return hash(HashHelper::getHashAlgo(), $input);
  }

}
