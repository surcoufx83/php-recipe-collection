<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

interface IFilesystemHelper {

  public static function file_exists(string $filename) : bool;
  public static function file_put_contents(string $filename, $data, ?int $flags = 0);
  public static function paths_combine(...$paths) : string;

}
