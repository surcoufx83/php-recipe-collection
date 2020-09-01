<?php

namespace Surcouf\Cookbook\User;

use \DateTime;
use Surcouf\Cookbook\IDbObject;
use Surcouf\Cookbook\User;
use Surcouf\Cookbook\IUser;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;

use League\OAuth2\Client\Token\AccessToken;

if (!defined('CORE2'))
  exit;

class Session implements IDbObject {

  private $id, $userid, $time, $keep;
  private $user;
  private $oauthToken;
  private $changes = array();

  public function __construct(IUser $user, $data) {
    $this->id = intval($data['login_id']);
    $this->userid = intval($data['user_id']);
    $this->time = new DateTime($data['login_time']);
    $this->keep = ConverterHelper::to_bool($data['login_keep']);
    $this->oauthToken = (!is_null($data['login_oauthdata']) ? new AccessToken(json_decode($data['login_oauthdata'], true)) : null);
    if (!is_null($this->oauthToken))
      $this->renewToken();
  }

  public function destroy() : void {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtDELETE, 'user_logins');
    $query->where('user_logins', 'login_id', '=', $this->id)
          ->limit(1);
    $Controller->delete($query);
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getToken() : ?AccessToken {
    return $this->oauthToken;
  }

  public function isExpired() : bool {
    return ($this->isOAuthSession() && $this->oauthToken->hasExpired());
  }

  public function isOAuthSession() : bool {
    return !is_null($this->oauthToken);
  }

  public function keep() : bool {
    return $this->keep;
  }

  private function renewToken() : void {
    global $Controller;
    if ($this->oauthToken->hasExpired()) {
      $provider = $Controller->getOAuthProvider();
      try {
        $newToken = $provider->getAccessToken('refresh_token', [
          'refresh_token' => $this->oauthToken->getRefreshToken()
        ]);
        $this->oauthToken = $newToken;
        $this->changes['login_oauthdata'] = json_encode($newToken->jsonSerialize());
        $Controller->updateDbObject($this);
      } catch (\Exception $e) {
        var_dump('Exception refreshing OAuth token!', $e);
        exit;
        $Controller->Dispatcher()->forward($Controller->getLink('private:login-oauth2'));
      }
    }
  }

}
