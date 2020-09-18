<?php

$Controller->get(array(
  'pattern' => '/admin/settings',
  'fn' => 'ui_admin_config'
));

function ui_admin_config() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_settings'),
    'url' => $Controller->getLink('admin:settings'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:settings';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_settings_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_admin_config()
