<?php

use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\User\BlankUser;
use Surcouf\Cookbook\User\UserInterface;

class UserRejectAdminCommand extends Ahc\Cli\Input\Command
{

  private $un, $user;

  public function __construct() {
    parent::__construct('user:rejectadmin', 'Removes the administration rights of a user.');
    $this
      ->argument('username', 'User login name');
  }

  public function interact(Ahc\Cli\IO\Interactor $io) {
    global $Controller;

    if (!is_null($this->username)) {
      if (strpos($this->username, 'username=') > -1)
        $this->un = substr($this->username, 9);
      $this->un = $this->username;
    }

    if (!$this->un || $this->un == '')
      $this->un = $io->prompt('Enter a username for this user');
    $this->user = $Controller->getUser($this->un);
    while(is_null($this->user)) {
      $this->un = $io->prompt('Username not found. Try again');
      $this->user = $Controller->getUser($this->un);
    }

  }

  public function execute() {
    global $writer, $Controller;
    $io = $this->app()->io();

    if (!$this->user->isAdmin()) {
      $writer->error('The user \''.$this->user->getUsername().'\' is not an administrator.', true);
      return 0;
    }

    $Controller->startTransaction();
    if (!$this->user->rejectAdmin()) {
      $writer->error('Request denied. Could not revoke the permissions.', true);
      return 0;
    }
    $Controller->tearDown();
    $Controller->finishTransaction();
    $writer->write('User \''.$this->user->getUsername().'\' is no longer an administrator.', true);
    return 1;
  }
}

$app->add(new UserRejectAdminCommand, '', true);
