<?php

use Surcouf\Cookbook\Config\EConfigurationType;

class AlgoBenchmarkCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('setup:algorithms');
  }

  public function execute()
  {
    global $writer;

    $writer->error('Starting benchmark, please wait...', true);
    $iterations = 10000;

    $tops = array();
    $this->executeHashing($writer, $iterations, 8, $tops);
    $this->executeHashing($writer, $iterations, 32, $tops);
    $this->executeHashing($writer, $iterations, 256, $tops);

    $writer->table([
      [ 'Length' => 8,
        '1.' => $tops[8][0]['algo'].' ('.$tops[8][0]['length'].'): '.$tops[8][0]['speed'].'µs',
        '2.' => $tops[8][1]['algo'].' ('.$tops[8][1]['length'].'): '.$tops[8][1]['speed'].'µs',
        '3.' => $tops[8][2]['algo'].' ('.$tops[8][2]['length'].'): '.$tops[8][2]['speed'].'µs',
        '4.' => $tops[8][3]['algo'].' ('.$tops[8][3]['length'].'): '.$tops[8][3]['speed'].'µs',
        '5.' => $tops[8][4]['algo'].' ('.$tops[8][4]['length'].'): '.$tops[8][4]['speed'].'µs',
      ],
      [ 'Length' => 32,
        '1.' => $tops[32][0]['algo'].' ('.$tops[32][0]['length'].'): '.$tops[32][0]['speed'].'µs',
        '2.' => $tops[32][1]['algo'].' ('.$tops[32][1]['length'].'): '.$tops[32][1]['speed'].'µs',
        '3.' => $tops[32][2]['algo'].' ('.$tops[32][2]['length'].'): '.$tops[32][2]['speed'].'µs',
        '4.' => $tops[32][3]['algo'].' ('.$tops[32][3]['length'].'): '.$tops[32][3]['speed'].'µs',
        '5.' => $tops[32][4]['algo'].' ('.$tops[32][4]['length'].'): '.$tops[32][4]['speed'].'µs',
      ],
      [ 'Length' => 256,
        '1.' => $tops[256][0]['algo'].' ('.$tops[256][0]['length'].'): '.$tops[256][0]['speed'].'µs',
        '2.' => $tops[256][1]['algo'].' ('.$tops[256][1]['length'].'): '.$tops[256][1]['speed'].'µs',
        '3.' => $tops[256][2]['algo'].' ('.$tops[256][2]['length'].'): '.$tops[256][2]['speed'].'µs',
        '4.' => $tops[256][3]['algo'].' ('.$tops[256][3]['length'].'): '.$tops[256][3]['speed'].'µs',
        '5.' => $tops[256][4]['algo'].' ('.$tops[256][4]['length'].'): '.$tops[256][4]['speed'].'µs',
      ],
    ]);

    //var_dump($tops);

    return;

  }

  private function executeHashing(&$writer, $iterations, $length, &$tops) : void {
    $algs = hash_algos();
    $speeds = array();
    $values = array();
    $icount = count($algs);
    $data = $this->generateRandomString($length);

    for ($i = 0; $i < $icount; $i++) {
      $start = microtime(true);
      for ($j = 0; $j < $iterations; $j++) {
        $value = hash($algs[$i], $data);
      }
      $time = number_format((microtime(true) - $start) * 1000000, 2, '.', '');
      $values[$algs[$i]] = $value;
      if (!array_key_exists($time, $speeds))
        $speeds[$time] = array();
      $speeds[$time][] = $algs[$i];
    }

    ksort($speeds, SORT_NUMERIC);
    $res = array();
    $writer->write('Test with '.$iterations.' iterations and '.$length.' byte of data:', true);
    for ($i = 0; $i < count($speeds); $i++) {
      $speed = array_keys($speeds)[$i];
      for ($j = 0; $j < count($speeds[$speed]); $j++) {
        $res[] = [
          'algo' => $speeds[$speed][$j],
          'speed' => $speed,
          'sample' => $values[$speeds[$speed][$j]],
          'length' => strlen($values[$speeds[$speed][$j]]),
        ];
      }
    }

    $tops[$length] = $res;

  }

  private function generateRandomString($length) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }

}

$app->add(new AlgoBenchmarkCommand);
