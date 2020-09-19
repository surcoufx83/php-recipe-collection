<?php

namespace Surcouf\Cookbook\Helper;

use Surcouf\Cookbook\Controller;

if (!defined('CORE2'))
  exit;

final class HashHelper implements HashHelperInterface {

  /**
   * Creates a random string with a length of at least 8 bytes.
   *
   * @param int $length Specifies the length of the expected string (in bytes). Default: 32 bytes = 64 characters
   * @return string|null The generated random string, or zero if the functions random_bytes and openssl_random_pseudo_bytes are not available on the server.
   */
  public static function generate_token(int $length = 32) : ?string {
      if(!isset($length) || intval($length) <= 8 ){
        $length = 32;
      }
      if (function_exists('random_bytes')) {
          return bin2hex(random_bytes($length));
      }
      if (function_exists('openssl_random_pseudo_bytes')) {
          return bin2hex(openssl_random_pseudo_bytes($length));
      }
      return null;
  }

  /**
   * Returns the string hashing algorithm for checksums specified in the configuration.
   *
   * @return string The name of the algorithm, as specified in the configuration.
   */
  public static function getChecksumAlgo() : string {
    global $Controller;
    return $Controller->Config()->ChecksumProvider;
  }

  /**
   * Returns the string hashing algorithm specified in the configuration.
   *
   * @return string The name of the algorithm, as specified in the configuration.
   */
  public static function getHashAlgo() : string {
    global $Controller;
    return $Controller->Config()->HashProvider;
  }

  /**
   * Generates the hash value of an input string with an optionally specified algorithm.
   *
   * @param string $input     The string to hash.
   * @param string $algorithm The algorithm to be used. Optional, default: null. If not specified, the default algorithm is used according to configuration.
   * @return string The hashed string.
   */
  public static function hash(string $input, ?string $algorithm = null) : string {
    return hash((!is_null($algorithm) ? $algorithm : HashHelper::getHashAlgo()), $input);
  }

}
