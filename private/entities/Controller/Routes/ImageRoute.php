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
    $dummy = $Controller->Dispatcher()->getFromMatches('dummy') == 'dummy';
    $rawimg = $Controller->Dispatcher()->getFromMatches('raw') == 'raw';
    if ($dummy)
      self::dummy();
    $picid = $Controller->Dispatcher()->getFromMatches('pictureid');
    if (!is_null($picid))
      self::recipePicture(intval($picid), $rawimg);
    return true;
  }

  static function dummy() : void {
    global $Controller;
    $Controller->Dispatcher()->moved('/pictures/_dummy.jpg');
  }

  static function recipePicture(int $pictureId, bool $rawimg) : void {
    global $Controller;
    $picture = $Controller->OM()->Picture($pictureId);
    if (is_null($picture)) {
      $Controller->Dispatcher()->notFound();
      return;
    }
    $filename = $picture->getFullpath(!$rawimg);
    if (!FilesystemHelper::file_exists($filename)) {
      if (!$rawimg) {
        if (!$picture->createThumbnail())
          $Controller->Dispatcher()->notFound();
      }
      else
        $Controller->Dispatcher()->notFound();
    }
    $Controller->Dispatcher()->moved($picture->getPublicPath(!$rawimg));
    exit;
  }

}
