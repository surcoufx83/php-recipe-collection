<?php

namespace Surcouf\PhpArchive\User;

use \DateTime;
use Surcouf\PhpArchive\User;
use Surcouf\PhpArchive\Database\EQueryType;
use Surcouf\PhpArchive\Database\QueryBuilder;
use Surcouf\PhpArchive\Helper\ConverterHelper;

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
