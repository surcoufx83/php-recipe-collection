<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook;
use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\IUser;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\User\Session;
use \DateTime;

if (!defined('CORE2'))
  exit;

class User implements IUser, IDbObject, IHashable {

  private $controller = null;
  private $id, $firstname, $lastname, $name, $initials, $loginname, $passwordhash, $mailadress, $hash, $avatar;
  private $mailvalidationcode, $mailvalidated, $lastactivity;

  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['user_id']);
    $this->firstname = $dr['user_firstname'];
    $this->lastname = $dr['user_lastname'];
    $this->name = $dr['user_fullname'];
    $this->initials = strtoupper(substr($this->firstname, 0, 1).substr($this->lastname, 0, 1));
    $this->loginname = $dr['user_name'];
    $this->passwordhash = $dr['user_password'];
    $this->mailadress = $dr['user_email'];
    $this->mailvalidationsent = (!is_null($dr['user_email_validation']) ? $dr['user_email_validation'] : '');
    $this->mailvalidated = (!is_null($dr['user_email_validated']) ? new DateTime($dr['user_email_validated']) : '');
    $this->lastactivity = (!is_null($dr['user_last_activity']) ? new DateTime($dr['user_last_activity']) : '');
    $this->hash = $dr['user_hash'];
    $this->avatar = $dr['user_avatar'];
  }

  public function calculateHash() : string {
    global $Controller;
    $data = [
      $this->id,
      $this->firstname,
      $this->lastname,
      $this->initials,
      $this->loginname,
      $this->mailadress,
    ];
    $this->hash = HashHelper::hash(join($data));
    $this->changes['user_hash'] = $this->hash;
    $Controller->updateDbObject($this);
    return $this->hash;
  }

  public function createNewSession($keepSession) : bool {
    global $Controller;

    $session_token = HashHelper::generate_token(16);
    $session_password = HashHelper::generate_token(24);

    $session_password4hash = HashHelper::hash(substr($session_token, 0, 16), $Controller->Config()->HashProvider);
    $session_password4hash .= $session_password;
    $session_password4hash .= HashHelper::hash(substr($session_token, 16), $Controller->Config()->HashProvider);

    $hash_token = password_hash($session_token, PASSWORD_ARGON2I, ['threads' => 12]);
    $hash_password = password_hash($session_password4hash, PASSWORD_ARGON2I, ['threads' => 12]);

    if ($Controller->setSessionCookies($this->loginname, $session_token, $session_password, $keepSession)) {
      $query = new QueryBuilder(EQueryType::qtINSERT, 'user_logins');
      $query->columns(['user_id', 'login_type', 'login_token', 'login_password', 'login_keep'])
            ->values([$this->id, 1, $hash_token, $hash_password, $keepSession]);
      if ($Controller->insert($query)) {
        $this->session = new Session($this, array(
          'login_id' => 0,
          'user_id' => $this->id,
          'login_time' => (new DateTime())->format('Y-m-d H:i:s'),
          'login_keep' => $keepSession,
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
        $this->loginname,
        $this->mailadress,
      ];
      $this->avatar = AvatarsHelper::createAvatar(join($data), 'u');
      $this->changes['user_avatar'] = $this->avatar;
      $Controller->updateDbObject($this);
    }
    return $Controller->getLink('private:avatar:'.$this->avatar);
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
    return $this->loginname;
  }

  public function hasHash() : bool {
    return !is_null($this->hash);
  }

  public function verify($password) : bool {
    global $Controller;
    $start = microtime(true);
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
    if ($result = $Controller->select($query)) {
      while ($record = $result->fetch_assoc()) {
        if (password_verify($session_token, $record['login_token'])) {
          $pwdhash = HashHelper::hash(substr($session_token, 0, 16), $Controller->Config()->HashProvider);
          $pwdhash .= $session_password;
          $pwdhash .= HashHelper::hash(substr($session_token, 16), $Controller->Config()->HashProvider);

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
    }
    return false;
  }

}
