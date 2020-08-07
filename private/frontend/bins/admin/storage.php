<?php

$Controller->get(array(
  'pattern' => '/admin/storage',
  'fn' => 'ui_admin_storage_todo'
));

function ui_admin_storage_todo() {
  global $Controller, $OUT;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => 'Administration',
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => 'Speicher',
    'url' => $Controller->getLink('admin:storage'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:storage';
  $OUT['Page']['Heading1'] = '<<TODO>>';
} // ui_admin_storage_todo()
