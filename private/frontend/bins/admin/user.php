<?php

use Surcouf\Cookbook\User\BlankUser;
use Surcouf\Cookbook\Response\EOutputMode;

$Controller->get(array(
  'pattern' => '/admin/users',
  'fn' => 'ui_admin_users'
));

$Controller->get(array(
  'pattern' => '/admin/new-user',
  'fn' => 'ui_admin_newuser'
));

$Controller->post(array(
  'pattern' => '/admin/new-user',
  'fn' => 'ui_admin_post_newuser',
  'outputMode' => EOutputMode::JSON
));

function ui_admin_newuser() {
  global $Controller, $OUT, $twig;

  if (!$Controller->isAuthenticated() || !$Controller->User()->isAdmin())
    $Controller->Dispatcher()->forward('/');

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_user'),
    'url' => $Controller->getLink('admin:users'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_newUser'),
    'url' => $Controller->getLink('admin:new-user'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:users';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_newUser_title');
  $OUT['Page']['Scripts']['FormValidator'] = true;
  $OUT['Page']['Scripts']['Custom'][] = 'admin-new-user';
  $OUT['Content'] = $twig->render('views/admin/users/new-user.html.twig', $OUT);
} // ui_admin_newuser()

function ui_admin_post_newuser() {
  global $Controller, $OUT, $twig;

  if (!$Controller->isAuthenticated() || !$Controller->User()->isAdmin())
    $Controller->Dispatcher()->forward('/');

  $payload = $Controller->Dispatcher()->getPayload();

  if (!array_key_exists('firstname', $payload) || $payload['firstname'] == '' || is_null($payload['firstname']))
    return $response = $Controller->Config()->getResponseArray(80);

  if (!array_key_exists('lastname', $payload) || $payload['lastname'] == '' || is_null($payload['lastname']))
    return $response = $Controller->Config()->getResponseArray(80);

  if (!array_key_exists('email', $payload) || $payload['email'] == '' || is_null($payload['email']))
    return $Controller->Config()->getResponseArray(80);

  if (!array_key_exists('username', $payload) || $payload['username'] == '' || is_null($payload['username']))
    return $Controller->Config()->getResponseArray(80);

  $user = new BlankUser($payload['firstname'], $payload['lastname'], $payload['username'], $payload['email']);
  $response = [];
  if (!$user->save($response))
    return $response;
  if (!$user->sendActivationMail($response))
    return $response;

  $response = $Controller->Config()->getResponseArray(1);
  $response['userid'] = $user->getId();
  return $response;

} // ui_admin_post_newuser()

function ui_admin_users() {
  global $Controller, $OUT, $twig;

  if (!$Controller->isAuthenticated() || !$Controller->User()->isAdmin())
    $Controller->Dispatcher()->forward('/');

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_home'),
    'url' => $Controller->getLink('admin:main'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_admin_user'),
    'url' => $Controller->getLink('admin:users'),
  );

  $OUT['Page']['Actions'][] = array(
    'class' => 'btn-outline-blue',
    'text' => $Controller->Config()->Icons()->Add('fa-fw mr-1').$Controller->l('page_admin_user_btnCreate'),
    'url' => $Controller->getLink('admin:new-user'),
  );

  $OUT['Page']['Current'] = 'admin:main';
  $OUT['Page']['CurrentSub'] = 'admin:users';
  $OUT['Page']['Heading1'] = $Controller->l('page_admin_user_title');
} // ui_admin_users()
