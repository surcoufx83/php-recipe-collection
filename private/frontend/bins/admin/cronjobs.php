<?php

$Controller->get(array(
  'pattern' => '/admin/cronjobs',
  'fn' => 'ui_admin_cronjobs'
));

function ui_admin_cronjobs() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:cronjobs';
  $OUT['Page']['Heading1'] = 'Cronjobs';
} // ui_admin_cronjobs()
