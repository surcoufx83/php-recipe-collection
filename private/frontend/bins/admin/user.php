<?php

$Controller->get(array(
  'pattern' => '/admin/users',
  'fn' => 'ui_admin_users'
));

function ui_admin_users() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:settings';
  $OUT['Page']['Heading1'] = 'Benutzerverwaltung';
} // ui_admin_users()
