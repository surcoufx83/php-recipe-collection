<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\OAuth2Conf;
use \DateTime;

if (!defined('CORE2'))
  exit;

class OAuthUser extends User {

  public function __construct(string $userid) {
    $this->username = 'OAuth2::'.$userid.'@'.OAuth2Conf::OATH_PROVIDER;
    $this->mailadress = 'OAuth2::'.$userid.'@'.OAuth2Conf::OATH_PROVIDER;
    $this->oauthname = $userid;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'oauth_user_name', 'user_email', 'user_email_validated'],
      [$this->username, $this->oauthname, $this->mailadress, (new DateTime())->format(DTF_SQL)]
    );
    if ($result > -1) {
      $this->id = $result;
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    $response['message'] = $Controller->dberror();
    return false;
  }

}
