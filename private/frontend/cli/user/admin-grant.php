<?php

use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\User\BlankUser;
use Surcouf\Cookbook\User\UserInterface;

class UserGrantAdminCommand extends Ahc\Cli\Input\Command
{

  private $un, $user;

  public function __construct() {
    parent::__construct('user:grantadmin', 'Gives administration privileges to a user.');
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
    $this->user = $Controller->OM()->User($this->un);
    while(is_null($this->user)) {
      $this->un = $io->prompt('Username not found. Try again');
      $this->user = $Controller->OM()->User($this->un);
    }

  }

  public function execute() {
    global $writer, $Controller;
    $io = $this->app()->io();

    if ($this->user->isAdmin()) {
      $writer->error('The user \''.$this->user->getUsername().'\' has already been granted administration rights.', true);
      return 0;
    }

    if (!$this->user->hasRegistrationCompleted()) {
      $writer->error('The user \''.$this->user->getUsername().'\' has not yet completed the activation of the account.', true);
      return 0;
    }

    $Controller->startTransaction();
    if (!$this->user->grantAdmin()) {
      $writer->error('Request denied. Could not grant the user \''.$this->user->getUsername().'\' this permission.', true);
      return 0;
    }
    $Controller->tearDown();
    $Controller->finishTransaction();
    $writer->write('User \''.$this->user->getUsername().'\' has become an administrator.', true);
    return 1;
  }
}

$app->add(new UserGrantAdminCommand, '', true);
