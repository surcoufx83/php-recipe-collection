<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config;
use Surcouf\Cookbook\ConfigInterface;
use Surcouf\Cookbook\Config\Icon;
use Surcouf\Cookbook\Config\IconConfig;
use Surcouf\Cookbook\Controller\Dispatcher;
use Surcouf\Cookbook\Controller\ObjectManager;
use Surcouf\Cookbook\Database\DbConf;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Helper\DatabaseHelper;
use Surcouf\Cookbook\OAuth2Conf;
use Surcouf\Cookbook\Recipe\Cooking\CookingStep;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Ingredient;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Units\Unit;
use Surcouf\Cookbook\Recipe\Ingredients\Units\UnitInterface;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\Rating;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\Tag;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\BlankUser;
use Surcouf\Cookbook\User\OAuthUser;
use Surcouf\Cookbook\User\User;
use Surcouf\Cookbook\User\UserInterface;

use \League\OAuth2\Client\Token\AccessToken;
use \League\OAuth2\Client\Provider\GenericProvider;

if (!defined('CORE2'))
  exit;

final class Controller implements ControllerInterface {

  private $database, $currentUser;
  private $config, $dispatcher, $langcode, $linkProvider;
  private $ObjectManager;

  private $pictures = array();
  private $ratings = array();
  private $steps = array();
  private $tags = array();
  private $units = array();
  private $users = array();

  private $changedObjects = array();

  public function __construct() {
    $this->ObjectManager = new ObjectManager();
  }

  public function Config() : ConfigInterface {
    return $this->config;
  }

  public function Dispatcher() : Dispatcher {
    return $this->dispatcher;
  }

  public function Language() : string {
    return $this->langcode;
  }

  public function ObjectManager() : ObjectManager {
    return $this->ObjectManager;
  }

  public function OM() : ObjectManager {
    return $this->ObjectManager;
  }

  public function User() : ?UserInterface {
    return $this->currentUser;
  }

  public function addActivity(int $type, array $data, RecipeInterface $recipe, ?int $pictureId=null, ?int $ratingId=null, ?int $tagId=null) : void {
    $query = new QueryBuilder(EQueryType::qtINSERT, 'activities');
    $data = array_merge(
              $data, [
                'user_id' => $this->User()->getId(),
                'user_name' => $this->User()->getUsername(),
                'recipe_id' => $recipe->getId(),
                'recipe_name' => $recipe->getName(),
              ]);
    $query
      ->columns(['user_id', 'entry_type', 'entry_data', 'recipe_id', 'picture_id', 'rating_id', 'tag_id'])
      ->values([
        $this->User()->getId(),
        $type,
        json_encode($data),
        $recipe->getId(),
        $pictureId,
        $ratingId,
        $tagId
      ]);
    $this->insert($query);
  }

  public function cancelTransaction() : bool {
    $ret = $this->database->rollback();
    $this->database->autocommit(true);
    return $ret;
  }

  public function dberror() : string {
    return $this->database->error;
  }

  public function dbescape($value, string $separator = ', ') : string {
    if (is_null($value))
      return 'NULL';
    if (is_integer($value) || is_float($value))
      return $value;
    if (is_bool($value))
      return intval($value);
    if (is_array($value)) {
      for ($i=0; $i<count($value); $i++)
        $value[$i] = $this->dbescape($value[$i], $separator);
      return join($separator, $value);
    }
    $value = '\''.$this->database->real_escape_string($value).'\'';
    return $value;
  }

  public function delete(QueryBuilder &$qbuilder) : bool {
    $query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    return $result;
  }

  public function finishTransaction() : bool {
    $ret = $this->database->commit();
    if ($ret == false) {
      $this->database->rollback();
    }
    $this->database->autocommit(true);
    return $ret;
  }

  public function get(array $params) : void {
    $this->dispatcher->get($params);
  }

  public function getInsertId() : ?int {
    return $this->database->insert_id;
  }

  public function getLink(string $filter, ...$args) : ?string {
    $filter2 = str_replace(':', '_', $filter);
    return $this->linkProvider->$filter2($args);
  }

  public function getOAuthProvider() : GenericProvider {
    return new GenericProvider([
      'clientId'                => OAuth2Conf::OATH_CLIENTID,    // The client ID assigned to you by the provider
      'clientSecret'            => OAuth2Conf::OATH_CLIENT_SECRET,   // The client password assigned to you by the provider
      'redirectUri'             => $this->getLink('admin:oauth:redirect'),
      'urlAuthorize'            => $this->getLink('admin:oauth:auth'),
      'urlAccessToken'          => $this->getLink('admin:oauth:token'),
      'urlResourceOwnerDetails' => $this->getLink('admin:oauth:user'),
    ]);
  }

  public function getObject(string $className, int $id) : ?object {
    if (array_key_exists($className, $this->objects)
     && array_key_exists($id, $this->objects[$className])) {
      return $this->objects[$className][$id];
    }
    return null;
  }

  public function init() : void {
    global $i18n;
    $this->langcode = $i18n->getAppliedLang();
    $this->linkProvider = new Controller\LinkProvider();
    if (!$this->init_Database())
      exit;
    if (!$this->init_Config())
      exit;
    if (!$this->init_Dispatcher())
      exit;
  }

  private function init_Config() : bool {
    $this->config = new Config();
    define('MAINTENANCE', $this->config->MaintenanceMode);
    return true;
  }

  private function init_Database() : bool {
    try {
      $this->database = new \Mysqli(DbConf::DB_HOST, DbConf::DB_USER, DbConf::DB_PASSWORD, DbConf::DB_DATABASE);
      if ($this->database->connect_errno != 0) {
        exit('Error connecting to the database.');
      }
      $this->database->set_charset('utf8mb4');
    }
    catch (\Exception $e) {
      exit('Error connecting to the database.');
    }
    try {
      Database\Setup::checkAndPatch($this->database);
    }
    catch (\Exception $e) {
      exit($e->getMessage());
    }
    return true;
  }

  private function init_Dispatcher() : bool {
    $this->dispatcher = new Dispatcher($this);
    $this->login();
    return true;
  }

  public function insert(QueryBuilder &$qbuilder) : bool {
    $query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    return $result;
  }

  public function insertSimple(string $table, array $columns, array $data) : int {
    $query = new QueryBuilder(EQueryType::qtINSERT, $table);
    $query->columns($columns)
          ->values($data);
    if ($this->insert($query)) {
      $id = $this->getInsertId();
      return $this->getInsertId();
    }
    return -1;
  }

  public function isAuthenticated() : bool {
    return !is_null($this->currentUser);
  }

  public function l(string $key, ...$params) : string {
    $outval = lang($key, $params);
    return ($outval == '' ? 'MISSING translation: '.$key : $outval);
  }

  private function login() : bool {
    if (ISWEB)
      return $this->loginWithCookies();
    else
      return $this->loginCli();
  }

  private function loginCli() : bool {
    $this->currentUser = $this->loadUser(1);
    return !is_null($this->currentUser);
  }

  private function loginWithCookies() : bool {
    if (count($_COOKIE) == 0)
      return false;
    if (!array_key_exists($this->config->UserCookieName, $_COOKIE) ||
        !array_key_exists($this->config->SessionCookieName, $_COOKIE) ||
        !array_key_exists($this->config->PasswordCookieName, $_COOKIE)) {
      return false;
    }
    $uname = $_COOKIE[$this->config->UserCookieName];
    $user = $this->OM()->User($uname);
    if (is_null($user) || !$user->verifySession($_COOKIE[$this->config->SessionCookieName], $_COOKIE[$this->config->PasswordCookieName])) {
      $this->removeCookies();
      return false;
    }
    $this->currentUser = $user;
    $this->renewSession();
    return true;
  }

  public function loginWithOAuth(AccessToken $token, bool &$userCreated) : bool {
    $values = $token->getValues();
    if (!array_key_exists('user_id', $values))
      return false;
    $userid = 'OAuth2::'.$values['user_id'].'@'.OAuth2Conf::OATH_PROVIDER;
    $user = $this->OM()->User($userid);
    if (is_null($user)) {
      $user = new OAuthUser($values['user_id']);
      $response = [];
      if (!$user->save($response))
        return false;
      $userCreated = true;
    }
    $this->currentUser =& $user;
    $this->currentUser->createNewSession(true, $token);
    return true;
  }

  public function loginWithPassword(string $email, string $password, bool $keepSession, array &$response = null) : bool {
    if ($email == '' || $password == '') {
      $response = $this->config->getResponseArray(30);
      return false;
    }
    $user = $this->OM()->User($email);
    if (is_null($user) || !$user->verify($password)) {
      $response = $this->config->getResponseArray(30);
      return false;
    }
    $this->currentUser =& $user;
    $this->currentUser->createNewSession($keepSession);
    $response = $this->config->getResponseArray(31);
    return true;
  }

  public function logout() : void {
    if ($this->isAuthenticated()) {
      $this->isAuthenticated = false;
      $this->currentUser->getSession()->destroy();
      $this->currentUser = null;
    }
    $this->removeCookies();
  }

  public function on(string $method, array $params) : void {
    $this->dispatcher->on($method, $params);
  }

  public function post(array $params) : void {
    $this->dispatcher->post($params);
  }

  public function put(array $params) : void {
    $this->dispatcher->put($params);
  }

  private function removeCookies() : void {
    $keys = array_keys($_COOKIE);
    for($i=0; $i<count($keys); $i++) {
      setcookie($keys[$i], null, -1);
      unset($_COOKIE[$keys[$i]]);
    }
  }

  private function renewSession() : void {
    $session = $this->currentUser->getSession();
    $expires = 0;
    if ($session->keep()) {
      global $NOW;
      $expdatetime = $NOW->add($this->config->SessionLongExpirationTime);
      $expires = $expdatetime->getTimestamp();
    }
    $this->setCookie($this->config->UserCookieName, $_COOKIE[$this->config->UserCookieName], $expires);
    $this->setCookie($this->config->SessionCookieName, $_COOKIE[$this->config->SessionCookieName], $expires);
    $this->setCookie($this->config->PasswordCookieName, $_COOKIE[$this->config->PasswordCookieName], $expires);
  }

  public function select(QueryBuilder &$queryBuilder) : ?\mysqli_result {
    return DatabaseHelper::select($this->database, $queryBuilder);
    /*$query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    if (!is_a($result, 'mysqli_result'))
      return null;
    return $result;*/
  }

  public function selectCountSimple(string $table, string $filterColumn=null, string $filterValue=null) : int {
    $query = new QueryBuilder(EQueryType::qtSELECT, $table);
    $query->select([['*', EAggregationType::atCOUNT, 'count']]);
    if (!is_null($filterColumn))
      $query->where($table, $filterColumn, '=', $filterValue);
    return $this->select($query)->fetch_assoc()['count'];
  }

  public function selectFirst(QueryBuilder &$queryBuilder) : ?array {
    return DatabaseHelper::selectFirst($this->database, $queryBuilder);
    /*$query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    if (!is_a($result, 'mysqli_result'))
      return null;
    if ($result->num_rows == 0)
      return null;
    return $result->fetch_assoc();*/
  }

  public function selectObject(QueryBuilder &$queryBuilder, string $className) : ?object {
    return DatabaseHelper::selectObject($this->database, $queryBuilder, $className);
    /*$query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    if (!is_a($result, 'mysqli_result'))
      return null;
    if ($result->num_rows == 0)
      return null;
    return $result->fetch_assoc();*/
  }

  private function setCookie(string $name, string $value, int $expiration) : bool {
    return setcookie($name, $value, $expiration, '/');
  }

  public function setSessionCookies(string $userCookie, string $tokenCookie, string $passwordCookie, bool $longDuration) : bool {
    $expires = 0;
    if ($longDuration) {
      global $NOW;
      $expdatetime = $NOW->add($this->config->SessionLongExpirationTime);
      $expires = $expdatetime->getTimestamp();
    }
    return ($this->setCookie($this->config->UserCookieName, $userCookie, $expires)
      && $this->setCookie($this->config->SessionCookieName, $tokenCookie, $expires)
      && $this->setCookie($this->config->PasswordCookieName, $passwordCookie, $expires));
  }

  public function startTransaction() : bool {
    $this->database->autocommit(false);
    return $this->database->begin_transaction();
  }

  public function tearDown() : void {

    foreach ($this->changedObjects as $key => $object) {

      switch(get_class($object)) {

        case 'Surcouf\Cookbook\Recipe\Recipe':
          if (count($object->getDbChanges()) == 0)
            break;
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipes');
          $query->update($object->getDbChanges());
          $query->where('recipes', 'recipe_id', '=', $object->getId());
          $this->update($query);
          break;

        case 'Surcouf\Cookbook\Recipe\Cooking\CookingStep':
          if (count($object->getDbChanges()) == 0)
            break;
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_steps');
          $query->update($object->getDbChanges());
          $query->where('recipe_steps', 'step_id', '=', $object->getId());
          $this->update($query);
          break;

        case 'Surcouf\Cookbook\Recipe\Ingredients\Ingredient':
          if (count($object->getDbChanges()) == 0)
            break;
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_ingredients');
          $query->update($object->getDbChanges());
          $query->where('recipe_ingredients', 'ingredient_id', '=', $object->getId());
          $this->update($query);
          break;

        case 'Surcouf\Cookbook\User\BlankUser':
        case 'Surcouf\Cookbook\User\User':
          if (count($object->getDbChanges()) == 0)
            break;
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'users');
          $query->update($object->getDbChanges());
          $query->where('users', 'user_id', '=', $object->getId());
          $this->update($query);
          break;

        case 'Surcouf\Cookbook\User\Session':
          if (count($object->getDbChanges()) == 0)
            break;
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'user_logins');
          $query->update($object->getDbChanges());
          $query->where('user_logins', 'login_id', '=', $object->getId());
          $this->update($query);
          break;

      }


    }

  }

  public function update(QueryBuilder &$qbuilder) : bool {
    $query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    return $result;
  }

  public function updateDbObject(DbObjectInterface &$object) : void {
    $key = get_class($object).$object->getId();
    if (!array_key_exists($key, $this->changedObjects))
      $this->changedObjects[$key] = $object;
  }

}
