<?php

use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Response\EOutputMode;

$Controller->get(array(
  'pattern' => '/activate/(?<token>[0-9a-z]{12,})',
  'requiresAuthentication' => false,
  'fn' => 'ui_activation'
));

$Controller->post(array(
  'pattern' => '/activate-account/(?<token>[0-9a-z]{12,})',
  'requiresAuthentication' => false,
  'fn' => 'post_activation',
  'outputMode' => EOutputMode::JSON
));

$Controller->get(array(
  'pattern' => '/login',
  'requiresAuthentication' => false,
  'fn' => 'ui_login'
));

$Controller->post(array(
  'pattern' => '/login',
  'requiresAuthentication' => false,
  'fn' => 'post_login'
));

$Controller->get(array(
  'pattern' => '/oauth2/login(\?)?',
  'requiresAuthentication' => false,
  'fn' => 'ui_oauth2_login'
));

$Controller->get(array(
  'pattern' => '/oauth2/callback\?[^/]+',
  'requiresAuthentication' => false,
  'fn' => 'ui_oauth2_callback'
));

$Controller->get(array(
  'pattern' => '/logout',
  'requiresAuthentication' => false,
  'fn' => 'ui_logout'
));

$Controller->get(array(
  'pattern' => '/self-register',
  'isSelfregistration' => true,
  'fn' => 'ui_self_register'
));

$Controller->get(array(
  'pattern' => '/settings',
  'fn' => 'ui_settings'
));

function ui_activation() {
  global $Controller, $OUT, $twig;

  $token = $Controller->Dispatcher()->getMatchString('token');
  $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
  $query->where('users', 'user_email_validation', 'LIKE', $token)
        ->andWhere('users', 'user_email_validated', 'IS NULL');
  $result = $Controller->select($query);
  if (is_null($result) || $result->num_rows == 0) {
    $OUT['Page']['Scripts']['Custom'][] = 'auth-login';
    $OUT['Content'] = $twig->render('views/user/activation-not-required.html.twig', $OUT);
    return;
  }

  $user = $Controller->getUser($result->fetch_assoc());

  $OUT['User'] = $user;
  $OUT['Page']['Scripts']['FormValidator'] = true;
  $OUT['Page']['Scripts']['Custom'][] = 'activation';
  $OUT['Content'] = $twig->render('views/user/activation.html.twig', $OUT);
} // ui_activation()

function ui_login() {
  global $Controller, $OUT, $twig;
  if ($Controller->isAuthenticated())
    $Controller->Dispatcher()->forward($Controller->getLink('private:home'));
  $OUT['Page']['Scripts']['Custom'][] = 'auth-login';
  $OUT['Content'] = $twig->render('views/user/login.html.twig', $OUT);
} // ui_login()

function post_activation() {
  global $Controller;

  $token = $Controller->Dispatcher()->getMatchString('token');
  $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
  $query->where('users', 'user_email_validation', 'LIKE', $token)
        ->andWhere('users', 'user_email_validated', 'IS NULL');
  $result = $Controller->select($query);
  if (is_null($result) || $result->num_rows == 0)
    return $Controller->Config()->getResponseArray(11);

  $payload = $Controller->Dispatcher()->getPayload();
  $user = $Controller->getUser($result->fetch_assoc());

  if (!array_key_exists('user', $payload) || intval($payload['user'] != $user->getId()))
    return $Controller->Config()->getResponseArray(11);

  if (!array_key_exists('password1', $payload) || !array_key_exists('password2', $payload) ||
    $payload['password1'] != $payload['password2'])
    return $Controller->Config()->getResponseArray(11);

  $keepSession = false;
  if (array_key_exists('keepSession', $payload))
    $keepSession = ConverterHelper::to_bool($payload['keepSession']);

  $response = null;
  if ($user->setPassword($payload['password1'], '')) {
    $user->validateEmail($token);
    $Controller->loginWithPassword($user->getMail(), $payload['password1'], $keepSession, $response);
  }
  else
    $response = $Controller->Config()->getResponseArray(30);
  $response['isLoggedIn'] = $Controller->isAuthenticated();
  return $response;

} // post_activation()

function post_login() {
  global $Controller;

  $username = $password = null;
  $agreedStatement = false;
  $keepSession = false;
  $response = null;

  if (array_key_exists('loginUsername', $_POST)) {
    $username = $_POST['loginUsername'];
  }

  if (array_key_exists('loginPassword', $_POST)) {
    $password = $_POST['loginPassword'];
  }

  if (array_key_exists('keepSession', $_POST)) {
    $keepSession = ($_POST['keepSession'] == 'true');
  }

  if ($password != null && $username != null) {
    if (!$Controller->loginWithPassword($username, $password, $keepSession, $response))
      $Controller->Dispatcher()->exitJson($response);
  }

  $response['isLoggedIn'] = $Controller->isAuthenticated();
  $Controller->Dispatcher()->exitJson($response);

} // post_login()

function ui_oauth2_login() {
  global $Controller;
  if ($Controller->isAuthenticated())
    $Controller->Dispatcher()->forward($Controller->getLink('private:home'));
  $Controller->Dispatcher()->startOAuthLogin();
  exit;
} // ui_oauth2_login()

function ui_oauth2_callback() {
  global $Controller, $OUT, $twig;

  // if login successfull, dispatcher will forward the user
  $Controller->Dispatcher()->finishOAuthLogin();

  $OUT['LoginFailed'] = true;

  $OUT['Page']['Scripts']['Custom'][] = 'auth-login';
  $OUT['Content'] = $twig->render('views/user/login.html.twig', $OUT);
} // ui_oauth2_callback()

function ui_logout() {
  global $Controller;
  $Controller->logout();
  $Controller->Dispatcher()->forward('/');
} // ui_logout()

function ui_self_register() {
  global $Controller, $OUT, $twig;
  if ($Controller->Dispatcher()->queryOAuthUserData())
    $Controller->Dispatcher()->forward($Controller->getLink('private:home'));
} // ui_self_register()

function ui_settings() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Current'] = 'private:home';
  $OUT['Page']['CurrentSub'] = 'private:settings';
  $OUT['Page']['Heading1'] = 'Meine Einstellungen';
} // ui_settings()
