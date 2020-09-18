<?php

$Controller->get(array(
  'pattern' => '/admin/translation',
  'fn' => 'ui_admin_translation'
));

function ui_admin_translation() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_translation'),
    'url' => $Controller->getLink('admin:translation'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:translation';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_translation_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_admin_translation()
