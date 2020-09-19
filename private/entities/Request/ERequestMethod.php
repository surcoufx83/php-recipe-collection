<?php

namespace Surcouf\Cookbook\Request;

if (!defined('CORE2'))
  exit;

final class ERequestMethod {

  const Unknown       = false;
  const HTTP_GET      = 'GET';
  const HTTP_POST     = 'POST';
  const HTTP_PUT      = 'PUT';
  const HTTP_HEAD     = 'HEAD';
  const HTTP_DELETE   = 'DELETE';
  const HTTP_PATCH    = 'PATCH';
  const HTTP_OPTIONS  = 'OPTIONS';
  const CLI           = 'CLI';

}
