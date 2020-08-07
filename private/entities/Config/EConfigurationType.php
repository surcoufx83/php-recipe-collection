<?php

namespace Surcouf\PhpArchive\Config;

if (!defined('CORE2'))
  exit;

class EConfigurationType {
  const TypeUnknown = 0;
  const TypeGroup = 1;

  const TypeArray = 2;
  const TypeBoolean = 3;
  const TypeDateTime = 4;
  const TypeFloat = 5;
  const TypeInt = 6;
  const TypeString = 7;
  const TypeTimespan = 8;

  const TypeIcon = 9;
  const TypeMailAccount = 10;
  const TypeResponseCode = 11;

}
