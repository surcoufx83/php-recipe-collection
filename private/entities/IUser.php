<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\User\Session;
use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

interface IUser {

  public function agreedToAds() : bool;
  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool;
  public function getAvatarUrl() : string;
  public function getFirstname() : string;
  public function getId() : int;
  public function getInitials() : string;
  public function getLastname() : string;
  public function getLastActivityTime() : ?\DateTime;
  public function getMail() : string;
  public function getName() : string;
  public function getSession() : ?Session;
  public function getUsername() : string;
  public function getValidationCode() : string;
  public function hasRegistrationCompleted() : bool;
  public function isAdmin() : bool;
  public function isOAuthUser() : bool;
  public function setPassword(string $newPassword, string $oldPassword) : bool;
  public function validateEmail(string $token) : bool;
  public function verify(string $password) : bool;
  public function verifySession(string $session_token, string $session_password) : bool;

}
