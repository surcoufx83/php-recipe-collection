<?php

class MaintenanceOffCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('maintenance:off', 'Exits the maintenance mode.');
  }

  public function execute()
  {
    global $writer, $Config;
    $io = $this->app()->io();
    if (!MAINTENANCE) {
      $writer->warn('Maintenance mode already disabled', true);
      return 1;
    } else {
      if (!$io->confirm('Please confirm: Disabling the maintenance mode', 'n')) {
        $writer->info('User has cancelled.', true);
        return 1;
      }
    }

    if ($Config->Maintenance->Enabled->setBoolean(false)) {
      $Config->Maintenance->DisplayMessage->setString('');
      $writer->error('Maintenance mode is now disabled!', true);
      return 1;
    } else {
      $writer->error('Error enabling maintenance mode: '.$Config->Maintenance->Enabled->errno.': '.$Config->Maintenance->Enabled->error, true);
      return -1;
    }

    return;

  }

}

$app->add(new MaintenanceOffCommand);
