<?php

use Surcouf\PhpArchive\Config\EConfigurationType;
use Surcouf\PhpArchive\Helper\Formatter;

class BenchmarkCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('setup:benchmark');
    $this->accesstests = array(
      'files/ocr' => array(
        'runs' => 1,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 0),
        EConfigurationType::TypeString => array('HomeTitle', 7),
        EConfigurationType::TypeIcon => array('iStorageByModule', 400),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file' => array(
        'runs' => 45,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 1),
        EConfigurationType::TypeString => array('HomeTitle', 9),
        EConfigurationType::TypeIcon => array('iStorageByModule', 1),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file/vali' => array(
        'runs' => 5,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 0),
        EConfigurationType::TypeString => array('HomeTitle', 7),
        EConfigurationType::TypeIcon => array('iStorageByModule', 13),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file/edit' => array(
        'runs' => 19,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 1),
        EConfigurationType::TypeString => array('HomeTitle', 9),
        EConfigurationType::TypeIcon => array('iStorageByModule', 12),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file/send' => array(
        'runs' => 10,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 1),
        EConfigurationType::TypeString => array('HomeTitle', 9),
        EConfigurationType::TypeIcon => array('iStorageByModule', 9),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file/ocr' => array(
        'runs' => 5,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 1),
        EConfigurationType::TypeString => array('HomeTitle', 12),
        EConfigurationType::TypeIcon => array('iStorageByModule', 23),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
      'file/classi' => array(
        'runs' => 15,
        EConfigurationType::TypeBoolean => array('Logging2Table', 1),
        EConfigurationType::TypeInt => array('lsModalEntriesCount', 0),
        EConfigurationType::TypeString => array('HomeTitle', 7),
        EConfigurationType::TypeIcon => array('iStorageByModule', 18),
        EConfigurationType::TypeResponseCode => array('resSuccess', 42),
      ),
    );
  }

  public function execute()
  {
    global $writer, $Config;

    $writer->error('Starting benchmark, please wait...', true);
    $iterations = 300;

    $this->executeDbBench($writer, $Config, $iterations);
    $this->executeConfigBench($writer, $Config, $iterations);

    return;

  }

  private function executeDbBench(&$writer, &$Config, $iterations) : void {
    $sum = 0;
    $writer->write('Simulating page config setup using database storage.');
    for ($i=0; $i<$iterations; $i++) {
      $Config = array();
      $start = microtime(true);
      Surcouf\PhpArchive\load_config();
      $sum += microtime(true) - $start;
      if ($i % 10 == 0)
        $writer->write('.');
    }
    $this->dbavg = $sum / $iterations * 1000;
    $writer->write('', true);
    $writer->write('Database config benchmarks with '.$iterations.' iterations completed.', true);
    $writer->write('Average time per iteration: ');
    $writer->ok(Formatter::float_format($this->dbavg, 2).'ms', true);
    $writer->write('', true);
  }

  private function executeConfigBench(&$writer, &$Config, $iterations) : void {
    $sum = 0;
    $writer->write('Simulating page config access.');
    $isum = ceil($iterations / 100);
    $result = array(
      EConfigurationType::TypeBoolean => array(0, 0.0),
      EConfigurationType::TypeInt => array(0, 0.0),
      EConfigurationType::TypeString => array(0, 0.0),
      EConfigurationType::TypeIcon => array(0, 0.0),
      EConfigurationType::TypeResponseCode => array(0, 0.0),
    );

    $start = microtime(true);
    for ($i=0; $i<$isum; $i++) {

      foreach ($this->accesstests as $key => $record) {

        $recordsum = 0;

        for ($a=0; $a<$record['runs']; $a++) {

          foreach ($record as $type => $props) {

            if ($type == 'runs')
              continue;

            $start2 = microtime(true);

            for ($b=0; $b<$props[1]; $b++) {
              switch ($type) {
                case EConfigurationType::TypeBoolean:
                  $Config->$props[0]->getBool();
                  break;
                case EConfigurationType::TypeInt:
                  $Config->$props[0]->getInt();
                  break;
                case EConfigurationType::TypeString:
                  $Config->$props[0]->getString();
                  break;
                case EConfigurationType::TypeIcon:
                  $Config->$props[0]->getIcon('');
                  break;
                case EConfigurationType::TypeResponseCode:
                  $Config->$props[0]->getResponseCode();
                  break;
              }
            }
            $result[$type][1] += microtime(true) - $start2;
            $result[$type][0] += $props[1];

          }

          if ($a % 10 == 0)
            $writer->write('.');

        }
      }

    }
    $sum += microtime(true) - $start;
    $this->confavg = $sum / $iterations * 1000;
    $writer->write('', true);
    $writer->write('Config access benchmarks with '.$isum.' iterations a 100 page requests completed.', true);
    $writer->write('Average time per iteration: ');
    $writer->ok(Formatter::float_format($this->confavg, 2).'ms', true);
    $writer->write('', true);
    var_dump($result);
  }

}

$app->add(new BenchmarkCommand, 'bm');
