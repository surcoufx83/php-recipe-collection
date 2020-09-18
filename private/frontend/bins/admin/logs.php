<?php

$Controller->get(array(
  'pattern' => '/admin/logs',
  'fn' => 'ui_admin_logs_todo'
));

function ui_admin_logs_todo() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_logs'),
    'url' => $Controller->getLink('admin:logs'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:logs';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_logs_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_admin_logs_todo()
