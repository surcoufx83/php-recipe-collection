<?php

$Controller->get(array(
  'pattern' => '/search',
  'fn' => 'ui_search'
));

function ui_search() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_search_header_title'),
    'url' => $Controller->getLink('private:books'),
  );

  $OUT['Page']['Current'] = 'private:search';
  $OUT['Page']['Heading1'] = $Controller->l('page_search_header_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_search()
