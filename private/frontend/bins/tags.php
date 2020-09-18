<?php

$Controller->get(array(
  'pattern' => '/tag/(?<id>\d+)(/[^/]+)?',
  'fn' => 'ui_tag'
));

function ui_tag() {
  global $Controller, $OUT, $twig;

  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_tag()
