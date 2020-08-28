<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

final class IconConfig {

  private $icons;

  public function __construct() {
    $this->icons = [
      'Add'               => ['space' => 'fas', 'icon' => 'plus-circle'],
      'Admin'             => ['space' => 'fas', 'icon' => 'users-cog'],
      'At'                => ['space' => 'fas', 'icon' => 'at'],
      'Authentication'    => ['space' => 'far', 'icon' => 'id-badge'],
      'Back'              => ['space' => 'fas', 'icon' => 'arrow-left'],
      'Ban'               => ['space' => 'fas', 'icon' => 'ban'],
      'Books'             => ['space' => 'fas', 'icon' => 'book'],
      'Delete'            => ['space' => 'far', 'icon' => 'trash-alt'],
      'DoubleAngleLeft'   => ['space' => 'fas', 'icon' => 'angle-double-left'],
      'DoubleAngleRight'  => ['space' => 'fas', 'icon' => 'angle-double-right'],
      'Download'          => ['space' => 'fas', 'icon' => 'download'],
      'Dummy'             => ['space' => 'fas', 'icon' => 'question'],
      'Edit'              => ['space' => 'fas', 'icon' => 'edit'],
      'Favorite'          => ['space' => 'fas', 'icon' => 'heart'],
      'Group'             => ['space' => 'fas', 'icon' => 'users'],
      'Home'              => ['space' => 'fas', 'icon' => 'home'],
      'Login'             => ['space' => 'fas', 'icon' => 'key'],
      'LoginError'        => ['space' => 'fas', 'icon' => 'skull-crossbones'],
      'Logout'            => ['space' => 'fas', 'icon' => 'sign-out-alt'],
      'Menu'              => ['space' => 'fas', 'icon' => 'bars'],
      'Password'          => ['space' => 'fas', 'icon' => 'key'],
      'QuestionMark'      => ['space' => 'far', 'icon' => 'question-circle'],
      'Random'            => ['space' => 'fas', 'icon' => 'dice'],
      'Save'              => ['space' => 'fas', 'icon' => 'save'],
      'Search'            => ['space' => 'fas', 'icon' => 'search'],
      'Settings'          => ['space' => 'fas', 'icon' => 'cog'],
      'Spinner'           => ['space' => 'fas', 'icon' => 'spinner'],
    ];
  }

  public function __call($methodName, $args) {
    $ico = new Icon(array_key_exists($methodName, $this->icons) ? $this->icons[$methodName] : $this->icons['Dummy']);
    switch (count($args)) {
      case 0:
        return $ico->getIcon();
      case 1:
        return $ico->getIcon($args[0]);
      case 2:
        return $ico->getIcon($args[0], $args[1]);
      case 3:
        return $ico->getIcon($args[0], $args[1], $args[2]);
    }
    throw new \Exception('Invalid argument count', 1);

  }

  public function __get($propertyName) : Icon {
    return new Icon(array_key_exists($propertyName, $this->icons) ? $this->icons[$propertyName] : $this->icons['Dummy']);
  }

}