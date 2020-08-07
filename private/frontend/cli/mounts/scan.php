<?php

class MountsScanCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('mounts:scan', 'Triggers a new scan of the entry points (mounts) and thus the comparison with the database.');
    $this->argument('[id]', 'Id of a mount for scanning. If the argument is omitted, all mounts are scanned.');
  }

  public function execute($id)
  {
    global $writer, $MountsByName;
    $table = array();
    foreach ($MountsByName as $key => $obj) {
      if (!is_null($id) && $obj->getId() != intval($id))
        continue;
      $response = array();
      $result = $obj->rescan($response);
      $table[] = array(
        'Id' => $obj->getId(),
        'Name' => $obj->getName(),
        'Success' => ConverterHelper::bool_to_str($result),
        'ReturnCode' => $response['Result']['Error']['Code'],
        'ReturnMessage' => $response['Result']['Error']['Message'],
      );
    }
    $writer->table($table);
    return 1;

  }
}

$app->add(new MountsScanCommand, 'ms');
