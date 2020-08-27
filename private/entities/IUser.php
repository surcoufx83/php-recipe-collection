<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\User\Session;

if (!defined('CORE2'))
  exit;

interface IUser {

  public function agreedToAds() : bool;
  public function createNewSession($keepSession) : bool;
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
  public function verify($password) : bool;
  public function verifySession(string $session_token, string $session_password) : bool;

}
