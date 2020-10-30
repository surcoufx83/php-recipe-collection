<?php

namespace Surcouf\Cookbook\Helper;

use Surcouf\Cookbook\Controller;
use BenMajor\ImageResize\Image;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;

if (!defined('CORE2'))
  exit;

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

final class FilesystemHelper implements FilesystemHelperInterface {

  public static function crop_image(PictureInterface $picture, int $width, int $height) : bool {
    $outname = $picture->getFullpath($width, $height);
    if (!FilesystemHelper::file_exists($picture->getFullpath()))
      return false;
    if (FilesystemHelper::file_exists($outname))
      return true;
    try {
      $copyfile = copy($picture->getFullpath(), $outname);
      $img = new Image($outname);
      $img->disableRename();
      if ($width != 0 && $height != 0)
        $img->resizeCrop($width, $height);
      else if ($width != 0)
        $img->resizeCrop($width);
      else
        $img->resizeCrop($height);
      $img->output(pathinfo($outname, PATHINFO_DIRNAME));
      return true;
    } catch(Exception $e) {
      return false;
    }
  }

  public static function crop_imageFile(string $fileIn, string $fileOut, int $width, int $height) : bool {
    if (!FilesystemHelper::file_exists($fileIn))
      return false;
    if (FilesystemHelper::file_exists($fileOut))
      return true;
    try {
      $copyfile = copy($fileIn, $fileOut);
      $img = new Image($fileOut);
      $img->disableRename();
      if ($width == 0 && $height == 0)
        $img->resizeCrop($width, $height);
      else if ($width != 0)
        $img->resizeCrop($width);
      else
        $img->resizeCrop($height);
      $img->output(pathinfo($fileOut, PATHINFO_DIRNAME));
      return true;
    } catch(Exception $e) {
      return false;
    }
  }

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
