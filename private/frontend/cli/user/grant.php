<?php

class UserGrantCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('user:grant', 'Grants permissions to the specified user.');
    $this
          ->argument('[id]', 'User id');;
  }

  public function interact(Ahc\Cli\IO\Interactor $io)
  {
    if (!$this->id) {
      global $UsersByName;
      $userkvl = array();
      foreach ($UsersByName as $key => $obj) {
        if ($obj->getId() == 1)
          continue;
        $userkvl[$obj->getId()] = $obj->getName();
      }
      $this->set('id', $io->choice('Select user: ', $userkvl));
    }

  }

  public function execute($id)
  {
    global $writer, $UsersByName;
    $io = $this->app()->io();
    $table = array();

    $groupkvl = array(
      1 => 'Administrative privileges',
      2 => 'Permissions for case module',
      3 => 'Permissions for documents',
      4 => 'Permissions for files',
      5 => 'Permissions for mounts',
      6 => 'Other general permissions',
    );
    
    foreach ($UsersByName as $key => $obj) {
      if ($obj->getId() == 1)
        continue;
      if ($obj->getId() != intval($id))
        continue;
      $this->set('grantgroup', $io->choice('Select rights category: ', $groupkvl));
    }
    $writer->table($table);
    return 1;
  }
}

$app->add(new UserGrantCommand, 'ug');
