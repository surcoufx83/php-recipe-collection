<?php

use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\User\UserInterface;

class UserListCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('user', 'Lists all registered users.');
  }

  public function execute()
  {
    global $writer, $Controller;
    $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
    $query->orderBy('users', ['user_name']);
    $result = $Controller->select($query);
    $table = array();
    while ($record = $result->fetch_assoc()) {
      $user = $Controller->getUser($record);
      $table[] = array(
        'Id' => $user->getId(),
        'Username' => $user->getUsername(),
        'Name' => $user->getName(),
        'E-Mail' => $user->getMail(),
        'Cloud user' => ConverterHelper::bool_to_str($user->isOAuthUser()),
        'Admin' => ConverterHelper::bool_to_str($user->isAdmin()),
      );
    }
    $writer->table($table);
    return 1;
  }
}

$app->add(new UserListCommand);
