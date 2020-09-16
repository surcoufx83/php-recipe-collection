<?php

namespace Surcouf\Cookbook\Database;

if (!defined('CORE2'))
  exit;

class EQueryType {

  const None = 0;

  const qtPREPARED_STMT = 1;

  const qtSELECT = 2;
  const qtUPDATE = 4;
  const qtINSERT = 8;
  const qtDELETE = 16;

}
