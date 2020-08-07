<?php

$Controller->get(array(
  'pattern' => '/admin/settings',
  'fn' => 'ui_admin_config'
));

function ui_admin_config() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:settings';
  $OUT['Page']['Heading1'] = 'Konfiguration';
} // ui_admin_config()
