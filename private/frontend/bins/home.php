<?php

$Controller->get(array(
  'pattern' => '/',
  'ignoreMaintenance' => true,
  'fn' => 'ui_home'
));

$Controller->get(array(
  'pattern' => '/',
  'ignoreMaintenance' => true,
  'requiresAuthentication' => false,
  'fn' => 'ui_anonymous_home'
));

$Controller->get(array(
  'pattern' => '/maintenance',
  'ignoreMaintenance' => true,
  'requiresAuthentication' => false,
  'fn' => 'ui_maintenance'
));

function ui_home() {
  global $Controller, $OUT;
  $OUT['Page']['Current'] = 'private:home';
  $OUT['Page']['Heading1'] = lang('greetings_hello', $Controller->User()->getFirstname());
} // ui_home()

function ui_anonymous_home() {
  global $OUT, $twig;
  $OUT['Content'] = $twig->render('views/user/home.html.twig', $OUT);
} // ui_anonymous_home()

function ui_maintenance() {
  global $OUT, $Router;
  if (!MAINTENANCE)
    $Router->forward('/');
  $OUT['Page']['Heading1'] = 'Maintenance mode - Restricted access';
} // ui_maintenance()
