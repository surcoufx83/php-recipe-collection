<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook;
use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\IUser;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\User\Session;
use \DateTime;

use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

class BlankUser implements IUser, IDbObject, IHashable {

  private $id, $firstname, $lastname, $name, $username, $oauthname, $initials, $passwordhash, $mailadress, $hash, $avatar, $isadmin;
  private $mailvalidationcode, $mailvalidated, $lastactivity, $registrationCompleted, $adconsent = false;

  private $changes = array();

  public function __construct(string $firstname, string $lastname, string $username, string $email) {
    $this->firstname = $firstname;
    $this->lastname = $lastname;
    $this->name = $firstname.' '.$lastname;
    $this->mailadress = $email;
    $this->username = $username;
    $this->registrationCompleted = new DateTime();
  }

  public function agreedToAds() : bool {
    return ($this->adconsent !== false);
  }

  public function calculateHash() : string {
    return '';
  }

  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool {
    return false;
  }

  public function getAvatarUrl() : string {
    return '';
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getFirstname() : string {
    return $this->firstname;
  }

  public function getHash(bool $calculateIfNull = true) : ?string {
    if (is_null($this->hash))
      $this->calculateHash();
    return $this->hash;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getInitials() : string {
    return $this->initials;
  }

  public function getLastname() : string {
    return $this->lastname;
  }

  public function getLastActivityTime() : ?\DateTime {
    return $this->lastactivity;
  }

  public function getMail() : string {
    return $this->mailadress;
  }

  public function getName() : string {
    return $this->name;
  }

  public function getSession() : ?Session {
    return $this->session;
  }

  public function getUsername() : string {
    return (!is_null($this->username) ? $this->username : $this->oauthname);
  }


  public function getValidationCode() : string {
    return $this->mailvalidationcode;
  }

  public function hasHash() : bool {
    return !is_null($this->hash);
  }

  public function hasRegistrationCompleted() : bool {
    return !is_null($this->registrationCompleted);
  }

  public function isAdmin() : bool {
    return false;
  }

  public function isOAuthUser() : bool {
    return !is_null($this->oauthname);
  }

  public function sendActivationMail(array &$response) : bool {
    global $Controller, $twig, $OUT;
    $mail = new Mail();
    $this->mailvalidationcode = HashHelper::generate_token(12);
    $OUT['ActivationLink'] = $Controller->getLink('private:activation', $this->mailvalidationcode);
    $data = [
      'Headline' => $Controller->l('sendmail_registration_activationMail_title'),
      'Content' => $twig->render('mails/activation-mail.html.twig', $OUT),
    ];
    if ($mail->send($this->name, $this->mailadress, $Controller->l('sendmail_registration_activationMail_subject'),  $data, $response)) {
      $this->changes['user_email_validation'] = $this->mailvalidationcode;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function setPassword(string $newPassword, string $oldPassword) : bool {
    return false;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'user_firstname', 'user_lastname', 'user_fullname', 'user_password', 'user_email', 'user_registration_completed'],
      [$this->username, $this->firstname, $this->lastname, $this->name, '********', $this->mailadress, $this->registrationCompleted->format(DTF_SQL)]
    );
    if ($result > -1) {
      $this->id = $result;
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    $response['message'] = $Controller->dberror();
    return false;
  }

  public function validateEmail(string $token) : bool {
    return false;
  }

  public function verify($password) : bool {
    return false;
  }

  public function verifySession(string $session_token, string $session_password) : bool {
    return false;
  }

}
