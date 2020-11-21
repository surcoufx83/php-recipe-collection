<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\HashableInterface;
use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\User\Session\Session;
use Surcouf\Cookbook\User\Session\SessionInterface;
use \DateTime;

use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

class User implements UserInterface, DbObjectInterface, HashableInterface {

  protected $user_id,
            $user_name,
            $oauth_user_name,
            $user_firstname,
            $user_lastname,
            $user_fullname,
            $initials,
            $user_hash,
            $user_isadmin,
            $user_password,
            $user_email,
            $user_email_validation,
            $user_email_validated,
            $user_last_activity,
            $user_avatar,
            $user_registration_completed,
            $user_adconsent = false;
  protected $recipe_count = -1;

  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->user_id = intval($record['user_id']);
      $this->user_name = $record['user_name'];
      $this->oauth_user_name = $record['oauth_user_name'];
      $this->user_firstname = $record['user_firstname'];
      $this->user_lastname = $record['user_lastname'];
      $this->user_fullname = $record['user_fullname'];
      $this->user_hash = $record['user_hash'];
      $this->user_isadmin = (ConverterHelper::to_bool($record['user_isadmin']) && !is_null($record['user_email_validated']));
      $this->user_password = $record['user_password'];
      $this->user_email = $record['user_email'];
      $this->user_email_validation = (!is_null($record['user_email_validation']) ? $record['user_email_validation'] : '');
      $this->user_email_validated = (!is_null($record['user_email_validated']) ? new DateTime($record['user_email_validated']) : '');
      $this->user_last_activity = (!is_null($record['user_last_activity']) ? new DateTime($record['user_last_activity']) : '');
      $this->user_avatar = $record['user_avatar'];
      $this->user_registration_completed = (!is_null($record['user_registration_completed']) ? new DateTime($record['user_registration_completed']) : null);
      $this->user_adconsent = (!is_null($record['user_adconsent']) ? new DateTime($record['user_adconsent']) : false);
    } else {
      $this->user_id = intval($this->user_id);
      $this->user_isadmin = (ConverterHelper::to_bool($this->user_isadmin) && !is_null($this->user_email_validated));
      $this->user_email_validation = (!is_null($this->user_email_validation) ? $this->user_email_validation : '');
      $this->user_email_validated = (!is_null($this->user_email_validated) ? new DateTime($this->user_email_validated) : '');
      $this->user_last_activity = (!is_null($this->user_last_activity) ? new DateTime($this->user_last_activity) : '');
      $this->user_registration_completed = (!is_null($this->user_registration_completed) ? new DateTime($this->user_registration_completed) : null);
      $this->user_adconsent = (!is_null($this->user_adconsent) ? new DateTime($this->user_adconsent) : false);
    }
    if ($this->user_firstname != '' || $this->user_lastname != '')
      $this->initials = strtoupper(substr($this->user_firstname, 0, 1).substr($this->user_lastname, 0, 1));
    else
      $this->initials = strtoupper(substr($this->getUsername(), 0, 1));
    if (is_null($this->user_hash))
      $this->calculateHash();
    if (is_null($this->user_avatar))
      $this->getAvatarUrl();
  }

  public function agreedToAds() : bool {
    return ($this->user_adconsent !== false);
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->user_id,
      $this->user_firstname,
      $this->user_lastname,
      $this->initials,
      $this->user_email,
    ];
    $this->user_hash = HashHelper::hash(join($data));
    $this->changes['user_hash'] = $this->user_hash;
    $Controller->updateDbObject($this);
    return $this->user_hash;
  }

  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool {
    global $Controller;
    $session_token = HashHelper::generate_token(16);
    $session_password = HashHelper::generate_token(24);
    $session_password4hash = HashHelper::hash(substr($session_token, 0, 16));
    $session_password4hash .= $session_password;
    $session_password4hash .= HashHelper::hash(substr($session_token, 16));
    $hash_token = password_hash($session_token, PASSWORD_ARGON2I, ['threads' => $Controller->Config()->System('Checksums', 'PwHashThreads')]);
    $hash_password = password_hash($session_password4hash, PASSWORD_ARGON2I, ['threads' => $Controller->Config()->System('Checksums', 'PwHashThreads')]);

    if ($Controller->setSessionCookies($this->user_name, $session_token, $session_password, $keepSession)) {
      $tokenstr = (!is_null($token) ? json_encode($token->jsonSerialize()) : NULL);
      $query = new QueryBuilder(EQueryType::qtINSERT, 'user_logins');
      $query->columns(['user_id', 'login_token', 'login_password', 'login_keep', 'login_oauthdata'])
            ->values([$this->user_id, $hash_token, $hash_password, $keepSession, $tokenstr]);
      if ($Controller->insert($query)) {
        $this->session = new Session($this, array(
          'login_id' => 0,
          'user_id' => $this->user_id,
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
    if (is_null($this->user_avatar) || !AvatarsHelper::exists($this->user_avatar)) {
      $data = [
        $this->user_id,
        $this->user_firstname,
        $this->user_lastname,
        $this->initials,
        $this->user_email,
      ];
      $this->user_avatar = AvatarsHelper::createAvatar(join($data), $this->user_id);
      $this->changes['user_avatar'] = $this->user_avatar;
      $Controller->updateDbObject($this);
    }
    return $Controller->getLink('private:avatar', $this->user_avatar);
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getFirstname() : string {
    return $this->user_firstname;
  }

  public function getHash(bool $calculateIfNull = true) : ?string {
    if (is_null($this->user_hash))
      $this->calculateHash();
    return $this->user_hash;
  }

  public function getId() : int {
    return $this->user_id;
  }

  public function getInitials() : string {
    return $this->initials;
  }

  public function getLastname() : string {
    return $this->user_lastname;
  }

  public function getLastActivityTime() : ?DateTime {
    return $this->user_last_activity;
  }

  public function getMail() : string {
    return $this->user_email;
  }

  public function getName() : string {
    return $this->user_fullname;
  }

  public function getRecipeCount() : int {
    if ($this->recipe_count == -1) {
      global $Controller;
      $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes');
      $query->select([['*', EAggregationType::atCOUNT, 'count']])
            ->where('recipes', 'recipe_public', '=', 1)
            ->andWhere('recipes', 'user_id', '=', $this->user_id);
      $this->recipe_count = $Controller->select($query)->fetch_assoc()['count'];
    }
    return $this->recipe_count;
  }

  public function getSession() : ?SessionInterface {
    return $this->session;
  }

  public function getUsername() : string {
    return (!is_null($this->oauth_user_name) ? $this->oauth_user_name : $this->user_name);
  }

  public function getValidationCode() : string {
    return $this->user_email_validation;
  }

  public function grantAdmin() : bool {
    global $Controller;
    if (ISWEB === false || (
      $Controller->isAuthenticated() &&
      $Controller->User()->isAdmin()
    )) {
      $this->user_isadmin = true;
      $this->changes['user_isadmin'] = $this->user_isadmin;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function hasHash() : bool {
    return !is_null($this->user_hash);
  }

  public function hasRegistrationCompleted() : bool {
    return !is_null($this->user_registration_completed);
  }

  public function isAdmin() : bool {
    return $this->user_isadmin;
  }

  public function isOAuthUser() : bool {
    return !is_null($this->oauth_user_name);
  }

  public function rejectAdmin() : bool {
    global $Controller;
    if (ISWEB === false || (
      $Controller->isAuthenticated() &&
      $Controller->User()->isAdmin()
    )) {
      $this->user_isadmin = false;
      $this->changes['user_isadmin'] = $this->user_isadmin;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function setFirstname(string $newValue) : void {
    global $Controller;
    $this->user_firstname = $newValue;
    $this->user_fullname = $this->user_firstname.' '.$this->user_lastname;
    $this->changes['user_firstname'] = $this->user_firstname;
    $this->changes['user_fullname'] = $this->user_fullname;
    $Controller->updateDbObject($this);
  }

  public function setLastname(string $newValue) : void {
    global $Controller;
    $this->user_lastname = $newValue;
    $this->user_fullname = $this->user_firstname.' '.$this->user_lastname;
    $this->changes['user_lastname'] = $this->user_lastname;
    $this->changes['user_fullname'] = $this->user_fullname;
    $Controller->updateDbObject($this);
  }

  public function setMail(string $newValue) : bool {
    global $Controller;
    if ($newValue != '') {
      $filter = filter_var($newValue, FILTER_VALIDATE_EMAIL);
      if ($filter == false)
        return false;
      $newuser = $Controller->OM()->User($newValue);
      if (!is_null($newuser))
        return false;
    }
    $this->user_email = $newValue;
    $this->changes['user_email'] = $this->user_email;
    $Controller->updateDbObject($this);
    return true;
  }

  public function setName(string $newValue) : void {
    global $Controller;
    $this->user_fullname = $newValue;
    $this->changes['user_fullname'] = $this->user_fullname;
    $parts = explode(' ', $this->user_fullname);
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
    if ($this->user_password == '********' || password_verify($oldPassword, $this->user_password)) {
      $this->user_password = password_hash($newPassword, PASSWORD_ARGON2I, ['threads' => $Controller->Config()->System('Checksums', 'PwHashThreads')]);
      $this->changes['user_password'] = $this->user_password;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function setRegistrationCompleted() : void {
    global $Controller;
    $this->user_registration_completed = new DateTime();
    $this->changes['user_registration_completed'] = $this->user_registration_completed->format(DTF_SQL);
    $Controller->updateDbObject($this);
  }

  public function validateEmail(string $token) : bool {
    global $Controller;
    if ($this->user_email_validation == $token) {
      $this->user_email_validation = '';
      $this->user_email_validated = new DateTime();
      $this->changes['user_email_validation'] = '';
      $this->changes['user_email_validated'] = $this->user_email_validated->format(DTF_SQL);
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function verify(string $password) : bool {
    global $Controller;
    if (password_verify($password, $this->user_password)) {
      if (password_needs_rehash($this->user_password, PASSWORD_ARGON2I, ['threads' => $Controller->Config()->System('Checksums', 'PwHashThreads')])) {
        $this->user_password = password_hash($password, PASSWORD_ARGON2I, ['threads' => $Controller->Config()->System('Checksums', 'PwHashThreads')]);
        $this->changes['user_password'] = $this->user_password;
        $Controller->updateDbObject($this);
      }
      return true;
    }
    return false;
  }

  public function verifySession(string $session_token, string $session_password) : bool {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'user_logins', DB_ANY);
    $query->where('user_logins', 'user_id', '=', $this->user_id)
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
