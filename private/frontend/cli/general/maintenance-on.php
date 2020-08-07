<?php

class MaintenanceOnCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('maintenance:on', 'Activates the maintenance mode.');
  }

  public function execute()
  {
    global $writer, $Config;
    $io = $this->app()->io();
    if (MAINTENANCE) {
      $writer->warn('Maintenance mode already enabled: '.$Config->Maintenance->DisplayMessage->getString(), true);
      if (!$io->confirm('Do you want to change the maintenance message?', 'n')) {
        $writer->info('User has cancelled.', true);
        return 1;
      }
    } else {
      if (!$io->confirm('Please confirm: Activating the maintenance mode', 'n')) {
        $writer->info('User has cancelled.', true);
        return 1;
      }
    }

    $msg = $io->prompt('Please enter a maintenance message', '', null, 0);
    if (MAINTENANCE || $Config->Maintenance->Enabled->setBoolean(true)) {
      $Config->Maintenance->DisplayMessage->setString($msg);
      $writer->error('Maintenance mode is now enabled!', true);
      $writer->write('Maintenance message: '.$msg, true);
      return 1;
    } else {
      $writer->error('Error enabling maintenance mode: '.$Config->Maintenance->Enabled->dberrno.': '.$Config->Maintenance->Enabled->dberror, true);
      return -1;
    }

    return;

  }

}

$app->add(new MaintenanceOnCommand);
