<?php

$speeddiag = [];
$speeddiag[] = [microtime(true), 0.0, __FILE__];

function spddg(string $file, string $fn='', string $class='', string $method='', string $p0='') {
  global $speeddiag;
  $speeddiag[] = [
    microtime(true),
    microtime(true) - $speeddiag[0][0],
    $file,
    $fn,
    $class,
    $method,
    $p0
  ];
}

require '../core.php';
