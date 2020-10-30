<?php

namespace Surcouf\Cookbook\Database;

if (!defined('CORE2'))
  exit;

final class EAggregationType {

  const None = 0;

  const atCOUNT = 1;
  const atDISTINCT = 2;

}
