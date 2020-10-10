<?php

namespace Surcouf\Cookbook\Database;

if (!defined('CORE2'))
  exit;

final class EAggregationType {

  const None = 0;

  const atAVG             = 1;
  const atCONCAT          = 2;
  const atCOUNT           = 4;
  const atDISTINCT        = 8;
  const atGRPCONCAT       = 16;
  const atMAX             = 32;
  const atMIN             = 64;
  const atSUM             = 128;
  const atIFNULL          = 256;
  const atISNULL          = 512;
  const atISNOTNULL       = 1024;

}
