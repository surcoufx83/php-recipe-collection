<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook;
use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\IUser;
use Surcouf\Cookbook\OAuth2Conf;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\User\Session;
use \DateTime;

use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

class OAuthUser implements IUser, IDbObject, IHashable {

  private $id, $firstname, $lastname, $name, $username, $oauthname, $initials, $passwordhash, $mailadress, $hash, $avatar, $isadmin;
  private $mailvalidationcode, $mailvalidated, $lastactivity, $registrationCompleted, $adconsent = false;

  private $changes = array();

  public function __construct(string $userid) {
    $this->username = 'OAuth2::'.$userid.'@'.OAuth2Conf::OATH_PROVIDER;
    $this->mailadress = 'OAuth2::'.$userid.'@'.OAuth2Conf::OATH_PROVIDER;
    $this->oauthname = $userid;
  }

  public function agreedToAds() : bool {
    return false;
  }

  public function calculateHash() : string {
    return '';
  }

  public function createNewSession(bool $keepSession, ?AccessToken $token=null) : bool {
    global $Controller;
    $session_token = HashHelper::generate_token(16);
    $session_password = HashHelper::generate_token(24);
    $session_password4hash = HashHelper::hash(substr($session_token, 0, 16), $Controller->Config()->HashProvider);
    $session_password4hash .= $session_password;
    $session_password4hash .= HashHelper::hash(substr($session_token, 16), $Controller->Config()->HashProvider);
    $hash_token = password_hash($session_token, PASSWORD_ARGON2I, ['threads' => 12]);
    $hash_password = password_hash($session_password4hash, PASSWORD_ARGON2I, ['threads' => 12]);

    if ($Controller->setSessionCookies($this->mailadress, $session_token, $session_password, $keepSession)) {
      $tokenstr = (!is_null($token) ? json_encode($token->jsonSerialize()) : NULL);
      $query = new QueryBuilder(EQueryType::qtINSERT, 'user_logins');
      $query->columns(['user_id', 'login_type', 'login_token', 'login_password', 'login_keep', 'login_oauthdata'])
            ->values([$this->id, 1, $hash_token, $hash_password, $keepSession, $tokenstr]);
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

  public function setPassword(string $newPassword, string $oldPassword) : bool {
    return false;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'oauth_user_name', 'user_email', 'user_email_validated'],
      [$this->username, $this->oauthname, $this->mailadress, (new \DateTime())->format(DTF_SQL)]
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
