<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config\IconConfigInterface;

if (!defined('CORE2'))
  exit;

interface ConfigInterface {

  public function __call(string $methodName, array $params);
  public function __get(string $propertyName);
  public function getResponseArray(int $responseCode) : array;
  public function Icons() : IconConfigInterface;

}
