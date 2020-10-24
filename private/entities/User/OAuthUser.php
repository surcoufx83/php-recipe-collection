<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\OAuth2Conf;
use \DateTime;

if (!defined('CORE2'))
  exit;

class OAuthUser extends User {

  public function __construct(string $userid) {
    global $Controller;
    $this->user_name = 'OAuth2::'.$userid.'@'.$Controller->Config()->System('OAUTH2', 'DisplayName');
    $this->oauth_user_name = $userid;
    $this->user_email = 'OAuth2::'.$userid.'@'.$Controller->Config()->System('OAUTH2', 'DisplayName');
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'oauth_user_name', 'user_email', 'user_email_validated'],
      [$this->user_name, $this->oauth_user_name, $this->user_email, (new DateTime())->format(DTF_SQL)]
    );
    if ($result > -1) {
      $this->user_id = $result;
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    $response['message'] = $Controller->dberror();
    return false;
  }

}
