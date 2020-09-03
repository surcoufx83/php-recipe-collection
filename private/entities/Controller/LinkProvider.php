<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\OAuth2Conf;

if (!defined('CORE2'))
  exit;

class LinkProvider {

  private $routes = array();

  public function __construct() {
    $this->routes = [
      'admin' => [
        'ajax' => [
          'testEntity' => '/admin/test/entity',
        ],
        'cronjobs' => '/admin/cronjobs',
        'logs' => '/admin/logs',
        'main' => '/admin',
        'new-user' => '/admin/new-user',
        'new-user-post' => '/admin/new-user',
        'oauth' => [
          'auth' => class_exists('Surcouf\Cookbook\OAuth2Conf') ? OAuth2Conf::OATH_AUTHURL : null,
          'redirect' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/oauth2/callback',
          'token' => class_exists('Surcouf\Cookbook\OAuth2Conf') ? OAuth2Conf::OATH_TOKENURL : null,
          'user' => class_exists('Surcouf\Cookbook\OAuth2Conf') ? OAuth2Conf::OATH_DATAURL : null,
        ],
        'settings' => '/admin/settings',
        'user' => '/admin/user/%d/%s',
        'users' => '/admin/users',
      ],
      'maintenance' => '/maintenance',
      'private' => [
        'activation' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/activate/%s',
        'activatePassword' => '/activate-account/%s',
        'avatar' => '/pictures/avatars/%s',
        'books' => '/books',
        'login' => '/login',
        'login-oauth2' => '/oauth2/login',
        'logout' => '/logout',
        'home' => '/',
        'random' => '/random',
        'recipes' => '/myrecipes',
        'search' => '/search',
        'self-register' => '/self-register',
        'settings' => '/settings',
      ],
      'recipe' => [
        'new' => '/recipe/new',
        'picture' => [
          'link' => '/pictures/cbimages/%s',
        ],
        'postnew' => '/recipe/new',
        'publish' => '/recipe/publish/%d/%s',
        'show' => '/%d/%s',
        'sendVoting' => '/recipe/vote/%d/%s',
        'unpublish' => '/recipe/unpublish/%d/%s',
      ],
      'tag' => [
        'show' => '/tag/%d/%s',
      ],
      'user' => [
        'recipes' => '/user-recipes/%d/%s',
      ],
    ];
  }

  public function __call(string $methodName, array $args) : ?string {
    $splitname = explode('_', $methodName);
    return $this->findRecord($splitname, $this->routes, $args[0]);
  }

  public function __get(string $propertyName) : ?string {
    $splitname = explode('_', $propertyName);
    return $this->findRecord($splitname, $this->routes, []);
  }

  private function findRecord(array $keys, array $route, array $args) : ?string {
    $key = array_shift($keys);
    if (array_key_exists($key, $route)) {
      $item = $route[$key];
      if (is_array($item)) {
        return $this->findRecord($keys, $item, $args);
      }
      try {
        return vsprintf($item, $args);
      } catch (\Exception $e) { }
    }
    return null;
  }

}
