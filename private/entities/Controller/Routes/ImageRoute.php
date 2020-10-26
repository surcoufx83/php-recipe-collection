<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Helper\FilesystemHelper;

if (!defined('CORE2'))
  exit;

class ImageRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;
    $dummy = $Controller->Dispatcher()->getFromMatches('dummy');
    $w = $Controller->Dispatcher()->getFromMatches('w');
    $h = $Controller->Dispatcher()->getFromMatches('h');
    $w = is_null($w) ? 0 : intval($w);
    $h = is_null($h) ? 0 : intval($h);
    if (!is_null($dummy))
      self::dummy($w, $h);
    $picid = $Controller->Dispatcher()->getFromMatches('pictureid');
    if (!is_null($picid))
      self::recipePicture(intval($picid), $w, $h);
    return true;
  }

  static function dummy(int $w, int $h) : void {
    global $Controller;
    if (is_null($w) && is_null($h)) {
      $Controller->Dispatcher()->moved('/pictures/_dummy.jpg');
    }
    $filename = sprintf('_dummy%dx%d.jpg', $w, $h);
    $filepath = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, $filename);
    if (\file_exists($filepath)) {
      $Controller->Dispatcher()->moved('/pictures/'.$filename);
      return;
    }
    $inpath = FilesystemHelper::paths_combine(DIR_PUBLIC_IMAGES, '_dummy.jpg');
    if (FilesystemHelper::crop_imageFile($inpath, $filepath, $w, $h))
      $Controller->Dispatcher()->moved('/pictures/'.$filename);
    else
      $Controller->Dispatcher()->notFound();
  }

  static function recipePicture(int $pictureId, int $w, int $h) : void {
    global $Controller;
    $picture = $Controller->OM()->Picture($pictureId);
    if (is_null($picture)) {
      $Controller->Dispatcher()->notFound();
      return;
    }
    $filename = $picture->getFullpath($w, $h);
    if (\file_exists($filename)) {
      $Controller->Dispatcher()->moved($picture->getPublicPath($w, $h));
      exit;
    }
    if (FilesystemHelper::crop_image($picture, $w, $h))
      $Controller->Dispatcher()->moved($picture->getPublicPath($w, $h));
    else
      $Controller->Dispatcher()->notFound();
    exit;
  }

}
