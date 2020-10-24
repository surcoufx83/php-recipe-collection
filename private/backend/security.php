<?php

spddg(__FILE__);

if (!defined('CORE2'))
  exit;

if (ISWEB) {
  header('Referrer-Policy: no-referrer, strict-origin-when-cross-origin');
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
  header('X-Content-Type-Options: nosniff');
  header("X-XSS-Protection: 1; mode=block");
}
