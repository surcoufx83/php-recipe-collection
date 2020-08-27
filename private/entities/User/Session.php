<?php

namespace Surcouf\Cookbook\User;

use \DateTime;
use Surcouf\Cookbook\User;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;

if (!defined('CORE2'))
  exit;

class Session {

  private $id, $userid, $time, $keep;
  private $user;

  function __construct(User $user, $data) {
    $this->id = intval($data['login_id']);
    $this->userid = intval($data['user_id']);
    $this->time = new DateTime($data['login_time']);
    $this->keep = ConverterHelper::to_bool($data['login_keep']);
  }

  function destroy() {
    global $Controller;
    $query = new QueryBuilder(EQueryType::qtDELETE, 'user_logins');
    $query->where('user_logins', 'login_id', '=', $this->id)
          ->limit(1);
    $Controller->delete($query);
  }

  function keep() : bool {
    return $this->keep;
  }

}
