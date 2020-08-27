<?php

namespace Surcouf\Cookbook\Helper;

use Surcouf\Cookbook\Controller;

if (!defined('CORE2'))
  exit;

class FilesystemHelper implements IFilesystemHelper {

  public static function file_exists(string $filename) : bool {
    return file_exists($filename);
  }

  public static function file_put_contents(string $filename, $data, ?int $flags = 0) {
    return file_put_contents($filename, $data, $flags);
  }

  public static function paths_combine(...$paths) : string {
    $fullpath = '';
    foreach($paths AS $path) {
      if ($fullpath == '')
        $fullpath = $path;
      else
        $fullpath = $fullpath.DIRECTORY_SEPARATOR.$path;
    }
    return $fullpath;
  }

}
