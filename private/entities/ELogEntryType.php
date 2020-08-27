<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

class ELogEntryType {

  const letUndefined = 0;
  const letRecordCreated = 1;
  const letRecordUpdated = 2;
  const letRecordDeleted = 3;

}
