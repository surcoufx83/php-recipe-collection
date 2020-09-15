<?php

use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\User\BlankUser;
use Surcouf\Cookbook\User\UserInterface;

class UserCreateCommand extends Ahc\Cli\Input\Command
{

  private $fn, $ln, $un, $mail, $pwd, $pwd2, $nv;

  public function __construct() {
    parent::__construct('user:create', 'Create a new user account.');
    $this
      ->argument('[username]', 'User login name')
      ->argument('[firstname]', 'User first name')
      ->argument('[lastname]', 'User last name')
      ->argument('[mailaddress]', 'Email address of the user')
      ->option('--novalidate', 'Ignore mail address validation');
  }

  public function interact(Ahc\Cli\IO\Interactor $io) {
    global $Controller;

    if (strpos($this->firstname, 'firstname=') > -1)
      $this->fn = substr($this->firstname, 10);
    if (strpos($this->lastname, 'lastname=') > -1)
      $this->ln = substr($this->lastname, 9);
    if (strpos($this->username, 'username=') > -1)
      $this->un = substr($this->username, 9);
    if (strpos($this->mailaddress, 'mailaddress=') > -1)
      $this->mail = substr($this->mailaddress, 12);

    if (!$this->un || $this->un == '')
      $this->un = $io->prompt('Enter a username for this user');
    $user = $Controller->getUser($this->un);
    while(!is_null($user)) {
      $this->un = $io->prompt('Username already in use. Give another one');
      $user = $Controller->getUser($this->un);
    }

    if (!$this->fn || $this->fn == '')
      $this->fn = $io->prompt('Enter users firstname');

    if (!$this->ln || $this->ln == '')
      $this->ln = $io->prompt('Enter users lastname');

    if (!$this->mail || $this->mail == '')
      $this->mail = $io->prompt('Enter users email address');
    $user = $Controller->getUser($this->mail);
    while(!is_null($user)) {
      $this->mail = $io->prompt('Mailaddress already in use. Give another one');
      $user = $Controller->getUser($this->mail);
    }

    if (is_null($this->novalidate))
      $this->nv = !$io->confirm('Send email activation link?');

    if ($this->nv) {
      while(is_null($this->pwd)) {
        $this->pwd = $io->promptHidden('Enter user password');
      }
      while(is_null($this->pwd2) || $this->pwd != $this->pwd2) {
        $this->pwd2 = $io->promptHidden('Re-enter user password');
      }
    }

  }

  public function execute() {
    global $writer, $Controller;
    $io = $this->app()->io();

    $Controller->startTransaction();
    $user = new BlankUser($this->fn, $this->ln, $this->un, $this->mail);
    $response = [];
    if (!$user->save($response)) {
      $writer->error('User can\'t be saved: '.$response['message'], true);
      return 0;
    }
    if (!$this->nv) {
      if (!$user->sendActivationMail($response)) {
        $writer->error('Can\'t send activation mail: '.$response['message'], true);
        return 0;
      }
    } else {
      if (!$user->setPassword($this->pwd, $this->pwd)) {
        $writer->error('Password can\'t be saved.', true);
        return 0;
      }
      if (!$user->validateEmail($user->getValidationCode())) {
        $writer->error('Can\'t activate account.', true);
        return 0;
      }
      $Controller->tearDown();
    }
    $Controller->finishTransaction();
    $writer->write('User \''.$user->getUsername().'\' created with id '.$user->getId().'.', true);
    return 1;
  }
}

$app->add(new UserCreateCommand);
