<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

interface IconConfigInterface {

  public function __call(string $methodName, array $params) : IconInterface;
  public function __get(string $propertyName) : IconInterface;

}
