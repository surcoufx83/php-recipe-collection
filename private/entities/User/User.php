<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\User\Session\Session;
use Surcouf\Cookbook\User\Session\SessionInterface;
use \DateTime;

use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

class User implements UserInterface, DbObjectInterface, HashableInterface {

  protected $id, $firstname, $lastname, $name, $username, $oauthname, $initials, $passwordhash, $mailadress, $hash, $avatar, $isadmin;
  protected $mailvalidationcode, $mailvalidated, $lastactivity, $registrationCompleted, $adconsent = false;

  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['user_id']);
    $this->firstname = $dr['user_firstname'];
    $this->lastname = $dr['user_lastname'];
    $this->name = $dr['user_fullname'];
    $this->username = $dr['user_name'];
    $this->oauthname = $dr['oauth_user_name'];
    $this->initials = strtoupper(substr($this->firstname, 0, 1).substr($this->lastname, 0, 1));
    $this->passwordhash = $dr['user_password'];
    $this->mailadress = $dr['user_email'];
    $this->isadmin = (ConverterHelper::to_bool($dr['user_isadmin']) && !is_null($dr['user_email_validated']));
    $this->mailvalidationcode = (!is_null($dr['user_email_validation']) ? $dr['user_email_validation'] : '');
    $this->mailvalidated = (!is_null($dr['user_email_validated']) ? new DateTime($dr['user_email_validated']) : '');
    $this->lastactivity = (!is_null($dr['user_last_activity']) ? new DateTime($dr['user_last_activity']) : '');
    $this->registrationCompleted = (!is_null($dr['user_registration_completed']) ? new DateTime($dr['user_registration_completed']) : null);
    $this->adconsent = (!is_null($dr['user_adconsent']) ? new DateTime($dr['user_adconsent']) : false);
    $this->hash = $dr['user_hash'];
    $this->avatar = $dr['user_avatar'];
    if (is_null($this->hash))
      $this->calculateHash();
    if (is_null($this->avatar))
      $this->getAvatarUrl();
  }

  public function agreedToAds() : bool {
    return ($this->adconsent !== false);
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->id,
      $this->firstname,
      $this->lastname,
      $this->initials,
      $this->mailadress,
    ];
    $this->hash = HashHelper::hash(join($data));
    $this->changes['user_hash'] = $this->hash;
    $Controller->updateDbObject($this);
    return $this->hash;
  }

  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool {
    global $Controller;
    $session_token = HashHelper::generate_token(16);
    $session_password = HashHelper::generate_token(24);
    $session_password4hash = HashHelper::hash(substr($session_token, 0, 16));
    $session_password4hash .= $session_password;
    $session_password4hash .= HashHelper::hash(substr($session_token, 16));
    $hash_token = password_hash($session_token, PASSWORD_ARGON2I, ['threads' => 12]);
    $hash_password = password_hash($session_password4hash, PASSWORD_ARGON2I, ['threads' => 12]);

    if ($Controller->setSessionCookies($this->mailadress, $session_token, $session_password, $keepSession)) {
      $tokenstr = (!is_null($token) ? json_encode($token->jsonSerialize()) : NULL);
      $query = new QueryBuilder(EQueryType::qtINSERT, 'user_logins');
      $query->columns(['user_id', 'login_token', 'login_password', 'login_keep', 'login_oauthdata'])
            ->values([$this->id, $hash_token, $hash_password, $keepSession, $tokenstr]);
      if ($Controller->insert($query)) {
        $this->session = new Session($this, array(
          'login_id' => 0,
          'user_id' => $this->id,
          'login_time' => (new DateTime())->format('Y-m-d H:i:s'),
          'login_keep' => $keepSession,
          'login_oauthdata' => $tokenstr,
        ));
        return true;
      }
    }
    return false;
  }

  public function getAvatarUrl() : string {
    global $Controller;
    if (is_null($this->avatar) || !AvatarsHelper::exists($this->avatar)) {
      $data = [
        $this->id,
        $this->firstname,
        $this->lastname,
        $this->initials,
        $this->mailadress,
      ];
      $this->avatar = AvatarsHelper::createAvatar(join($data), $this->id);
      $this->changes['user_avatar'] = $this->avatar;
      $Controller->updateDbObject($this);
    }
    return $Controller->getLink('private:avatar', $this->avatar);
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

  public function getLastActivityTime() : ?DateTime {
    return $this->lastactivity;
  }

  public function getMail() : string {
    return $this->mailadress;
  }

  public function getName() : string {
    return $this->name;
  }

  public function getSession() : ?SessionInterface {
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
    return $this->isadmin;
  }

  public function isOAuthUser() : bool {
    return !is_null($this->oauthname);
  }

  public function setFirstname(string $newValue) : void {
    global $Controller;
    $this->firstname = $newValue;
    $this->changes['user_firstname'] = $this->firstname;
    $Controller->updateDbObject($this);
  }

  public function setLastname(string $newValue) : void {
    global $Controller;
    $this->lastname = $newValue;
    $this->changes['user_lastname'] = $this->lastname;
    $Controller->updateDbObject($this);
  }

  public function setMail(string $newValue) : void {
    global $Controller;
    $this->mailadress = $newValue;
    $this->changes['user_email'] = $this->mailadress;
    $Controller->updateDbObject($this);
  }

  public function setName(string $newValue) : void {
    global $Controller;
    $this->name = $newValue;
    $this->changes['user_fullname'] = $this->name;
    $parts = explode(' ', $this->name);
    if (count($parts) == 1)
      $this->setFirstname($parts[0]);
    elseif (count($parts) == 2) {
      $this->setFirstname($parts[0]);
      $this->setLastname($parts[1]);
    }
    elseif (count($parts) == 3) {
      $this->setFirstname(implode(' ', [$parts[0], $parts[1]]));
      $this->setLastname($parts[2]);
    }
    elseif (count($parts) == 3) {
      $this->setFirstname(implode(' ', [$parts[0], $parts[1]]));
      $this->setLastname(implode(' ', [$parts[2], $parts[3]]));
    }
    $Controller->updateDbObject($this);
  }

  public function setPassword(string $newPassword, string $oldPassword) : bool {
    global $Controller;
    if ($this->passwordhash == '********' || password_verify($oldPassword, $this->passwordhash)) {
      $this->passwordhash = password_hash($newPassword, PASSWORD_ARGON2I, ['threads' => 12]);
      $this->changes['user_password'] = $this->passwordhash;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function setRegistrationCompleted() : void {
    global $Controller;
    $this->registrationCompleted = new DateTime();
    $this->changes['user_registration_completed'] = $this->registrationCompleted->format(DTF_SQL);
    $Controller->updateDbObject($this);
  }

  public function validateEmail(string $token) : bool {
    global $Controller;
    if ($this->mailvalidationcode == $token) {
      $this->mailvalidationcode = '';
      $this->mailvalidated = new DateTime();
      $this->changes['user_email_validation'] = '';
      $this->changes['user_email_validated'] = $this->mailvalidated->format(DTF_SQL);
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function verify(string $password) : bool {
    global $Controller;
    if (password_verify($password, $this->passwordhash)) {
      if (password_needs_rehash($this->passwordhash, PASSWORD_ARGON2I, ['threads' => 12])) {
        $this->passwordhash = password_hash($password, PASSWORD_ARGON2I, ['threads' => 12]);
        $this->changes['user_password'] = $this->passwordhash;
        $Controller->updateDbObject($this);
      }
      return true;
    }
    return false;
  }

  public function verifySession(string $session_token, string $session_password) : bool {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'user_logins', DB_ANY);
    $query->where('user_logins', 'user_id', '=', $this->id)
          ->orderBy([['login_time', 'DESC']]);
    $result = $Controller->select($query);
    if (!$result || $result->num_rows == 0)
      return false;
    while ($record = $result->fetch_assoc()) {
      if (password_verify($session_token, $record['login_token'])) {
        $pwdhash = HashHelper::hash(substr($session_token, 0, 16));
        $pwdhash .= $session_password;
        $pwdhash .= HashHelper::hash(substr($session_token, 16));

        if (password_verify($pwdhash, $record['login_password'])) {
          $uptime = new DateTime();

          $query = new QueryBuilder(EQueryType::qtUPDATE, 'user_logins');
          $query->update(['login_time' => $uptime->format('Y-m-d H:i:s')]);
          $query->where('user_logins', 'login_id', '=', intval($record['login_id']));
          $Controller->update($query);

          $record['login_time'] = $uptime->format('Y-m-d H:i:s');
          $this->session = new Session($this, $record);
          return true;
        }
      }
    }
    return false;
  }

}
