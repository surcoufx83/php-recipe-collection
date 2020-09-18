<?php

$Controller->get(array(
  'pattern' => '/admin/cronjobs',
  'fn' => 'ui_admin_cronjobs'
));

function ui_admin_cronjobs() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_cronjobs'),
    'url' => $Controller->getLink('admin:cronjobs'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:cronjobs';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_cronjobs_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_admin_cronjobs()
