<?php

$Controller->get(array(
  'pattern' => '/admin/logs',
  'fn' => 'ui_admin_logs_todo'
));

function ui_admin_logs_todo() {
  global $Controller, $OUT;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => 'Administration',
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => 'Server-Logs',
    'url' => $Controller->getLink('admin:logs'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:logs';
  $OUT['Page']['Heading1'] = '<<TODO>>';
} // ui_admin_logs_todo()
