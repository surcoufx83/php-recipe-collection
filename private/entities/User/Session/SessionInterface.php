<?php

namespace Surcouf\Cookbook\User\Session;

use Surcouf\Cookbook\User\UserInterface;
use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

interface SessionInterface {

  public function destroy() : void;
  public function getId() : int;
  public function getToken() : ?AccessToken;
  public function getUser() : UserInterface;
  public function getUserId() : int;
  public function isExpired() : bool;
  public function isOAuthSession() : bool;
  public function keep() : bool;

}
