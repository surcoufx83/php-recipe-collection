<?php

namespace Surcouf\Cookbook\Helper;

use Surcouf\Cookbook\Controller;

if (!defined('CORE2'))
  exit;

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

class FilesystemHelper implements FilesystemHelperInterface {

  public static function file_exists(string $filename) : bool {
    return file_exists($filename);
  }

  public static function file_put_contents(string $filename, $data, ?int $flags = 0) {
    return file_put_contents($filename, $data, $flags);
  }

  public static function paths_combine(...$paths) : string {
    $paths = array_filter($paths, function($value) { return !is_null($value) && $value !== ''; });
    return join(DS, $paths);
  }

}
