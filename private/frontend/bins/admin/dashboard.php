<?php

$Controller->get(array(
  'pattern' => '/admin',
  'fn' => 'ui_admin_dashboard'
));

function ui_admin_dashboard() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:main';
  $OUT['Page']['Heading1'] = 'Admin Dashboard';
} // ui_admin_dashboard()
