<?php

class MountsListCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('mounts', 'Lists the registered entry points (mounts).');
  }

  public function execute()
  {
    global $writer, $MountsByName;
    load_mountStatistics();
    $table = array();
    foreach ($MountsByName as $key => $obj) {
      $table[] = array(
        'Id' => $obj->getId(),
        'Name' => $obj->getName(),
        'Folders' => ($obj->hasRoot() ? $obj->getDirCount() : 0),
        'Files' => ($obj->hasRoot() ? $obj->getFileCount(true) : 0),
        'Size' => ($obj->hasRoot() ? $obj->getFileSizeStr() : 0),
      );
    }
    $writer->table($table);
    return 1;

  }
}

$app->add(new MountsListCommand, 'm');
