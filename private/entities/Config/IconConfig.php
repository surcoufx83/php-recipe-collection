<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

final class IconConfig implements IconConfigInterface {

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
      'Cloud'             => ['space' => 'fas', 'icon' => 'cloud'],
      'Copy'              => ['space' => 'fas', 'icon' => 'copy'],
      'Delete'            => ['space' => 'far', 'icon' => 'trash-alt'],
      'DoubleAngleLeft'   => ['space' => 'fas', 'icon' => 'angle-double-left'],
      'DoubleAngleRight'  => ['space' => 'fas', 'icon' => 'angle-double-right'],
      'Download'          => ['space' => 'fas', 'icon' => 'download'],
      'Dummy'             => ['space' => 'fas', 'icon' => 'question'],
      'Edit'              => ['space' => 'fas', 'icon' => 'edit'],
      'Favorite'          => ['space' => 'fas', 'icon' => 'heart'],
      'Group'             => ['space' => 'fas', 'icon' => 'users'],
      'Home'              => ['space' => 'fas', 'icon' => 'home'],
      'Lock'              => ['space' => 'fas', 'icon' => 'lock'],
      'Login'             => ['space' => 'fas', 'icon' => 'key'],
      'LoginError'        => ['space' => 'fas', 'icon' => 'skull-crossbones'],
      'Logout'            => ['space' => 'fas', 'icon' => 'sign-out-alt'],
      'Meal'              => ['space' => 'fas', 'icon' => 'utensils'],
      'Menu'              => ['space' => 'fas', 'icon' => 'bars'],
      'Password'          => ['space' => 'fas', 'icon' => 'key'],
      'QuestionMark'      => ['space' => 'far', 'icon' => 'question-circle'],
      'Random'            => ['space' => 'fas', 'icon' => 'dice'],
      'Save'              => ['space' => 'fas', 'icon' => 'save'],
      'Search'            => ['space' => 'fas', 'icon' => 'search'],
      'Settings'          => ['space' => 'fas', 'icon' => 'cog'],
      'Spinner'           => ['space' => 'fas', 'icon' => 'spinner'],
      'Star'              => ['space' => 'fas', 'icon' => 'star'],
      'Unlock'            => ['space' => 'fas', 'icon' => 'lock-open'],
      'User'              => ['space' => 'fas', 'icon' => 'user-circle'],
      'View'              => ['space' => 'far', 'icon' => 'eye'],
    ];
  }

  public function __call(string $methodName, array $params) : string {
    $ico = new Icon(array_key_exists($methodName, $this->icons) ? $this->icons[$methodName] : $this->icons['Dummy']);
    switch (count($params)) {
      case 0:
        return $ico->getIcon();
      case 1:
        return $ico->getIcon($params[0]);
      case 2:
        return $ico->getIcon($params[0], $params[1]);
      case 3:
        return $ico->getIcon($params[0], $params[1], $params[2]);
    }
    throw new \Exception('Invalid argument count', 1);

  }

  public function __get(string $propertyName) : IconInterface {
    return new Icon(array_key_exists($propertyName, $this->icons) ? $this->icons[$propertyName] : $this->icons['Dummy']);
  }

}
