<?php

$Controller->get(array(
  'pattern' => '/admin',
  'fn' => 'ui_admin_dashboard'
));

function ui_admin_dashboard() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:main';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_dashboard_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_admin_dashboard()
