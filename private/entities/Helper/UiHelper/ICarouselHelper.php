<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

interface ICarouselHelper {

  public static function createNew(string $id) : array;

}
