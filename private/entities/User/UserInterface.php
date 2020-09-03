<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\User\Session\SessionInterface;
use League\OAuth2\Client\Token\AccessToken;
use \DateTime;

if (!defined('CORE2'))
  exit;

interface UserInterface {

  public function agreedToAds() : bool;
  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool;
  public function getAvatarUrl() : string;
  public function getFirstname() : string;
  public function getId() : int;
  public function getInitials() : string;
  public function getLastname() : string;
  public function getLastActivityTime() : ?DateTime;
  public function getMail() : string;
  public function getName() : string;
  public function getSession() : ?SessionInterface;
  public function getUsername() : string;
  public function getValidationCode() : string;
  public function hasRegistrationCompleted() : bool;
  public function isAdmin() : bool;
  public function isOAuthUser() : bool;
  public function setFirstname(string $newValue) : void;
  public function setLastname(string $newValue) : void;
  public function setMail(string $newValue) : void;
  public function setName(string $newValue) : void;
  public function setPassword(string $newPassword, string $oldPassword) : bool;
  public function setRegistrationCompleted() : void;
  public function validateEmail(string $token) : bool;
  public function verify(string $password) : bool;
  public function verifySession(string $session_token, string $session_password) : bool;

}
