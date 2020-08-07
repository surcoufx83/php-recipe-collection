<?php

namespace Surcouf\PhpArchive\Helper;

if (!defined('CORE2'))
  exit;

final class AvatarsHelper implements IAvatarsHelper {

  public static function createAvatar(string $payload, string $appendix) : string {
    $data = HashHelper::hash($payload);
    $filename = $data.$appendix.'.png';
    $filepath = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, 'avatars', $filename);
    $identicon = new \Identicon\Identicon();
    $imageData = $identicon->getImageData($payload);
    FilesystemHelper::file_put_contents($filepath, $imageData);
    return ($filename);
  }

  public static function exists(string $filename) : bool {
    $filepath = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, 'avatars', $filename);
    return FilesystemHelper::file_exists($filepath);
  }

}
