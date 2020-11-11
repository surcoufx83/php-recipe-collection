<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

final class Icon implements IconInterface {

  private $space, $icon;

  function __construct($data) {
    $this->icon = $data['icon'];
    $this->space = $data['space'];
  }

  public function getIcon() : string {
    return $this->icon;
  }

  public function getSpace() : string {
    return $this->space;
  }

}
