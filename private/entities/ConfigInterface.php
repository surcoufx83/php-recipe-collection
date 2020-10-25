<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

interface ConfigInterface {

  public function __call(string $methodName, array $params);
  public function getCredentials(object $obj, int $type) : bool;
  public function getIcon(string $key) : ?array;
  public function getIconKeys() : array;
  public function getResponseArray(int $responseCode) : array;

}
