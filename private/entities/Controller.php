<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config;
use Surcouf\Cookbook\ConfigInterface;
use Surcouf\Cookbook\Config\Icon;
use Surcouf\Cookbook\Config\IconConfig;
use Surcouf\Cookbook\Controller\Dispatcher;
use Surcouf\Cookbook\Database\DbConf;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\DbObjectInterface;
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

class Controller implements ControllerInterface {

  private $database, $currentUser;
  private $config, $dispatcher, $langcode, $linkProvider;

  private $ingredients = array();
  private $pictures = array();
  private $ratings = array();
  private $recipes = array();
  private $steps = array();
  private $tags = array();
  private $units = array();
  private $users = array();

  private $changedObjects = array();

  public function Config() : ConfigInterface {
    return $this->config;
  }

  public function Dispatcher() : Dispatcher {
    return $this->dispatcher;
  }

  public function Language() : string {
    return $this->langcode;
  }

  public function User() : ?UserInterface {
    return $this->currentUser;
  }

  public function cancelTransaction() : bool {
    $ret = $this->database->rollback();
    $this->database->autocommit(true);
    return $ret;
  }

  public function dberror() : string {
    return $this->database->error;
  }

  public function dbescape($value, bool $includeQuotes = true) : string {
    $value = $this->database->real_escape_string($value);
    if ($includeQuotes && !is_integer($value))
      $value = '\''.$value.'\'';
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

  public function getIngredient($filter) : ?IngredientInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->ingredients))
        return $this->loadIngredient($filter);
      else
        return $this->ingredients[$filter];
    }
    if (is_array($filter))
      return $this->registerIngredient(null, $filter);
    return null;
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

  public function getPicture($filter) : ?PictureInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->pictures))
        return $this->loadPicture($filter);
      else
        return $this->pictures[$filter];
    }
    if (is_array($filter))
      return $this->registerPicture(null, $filter);
    return null;
  }

  public function getRating($filter) : ?RatingInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->ratings))
        return $this->loadRating($filter);
      else
        return $this->ratings[$filter];
    }
    if (is_array($filter))
      return $this->registerRating(null, $filter);
    return null;
  }

  public function getRecipe($filter) : ?RecipeInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->recipes))
        return $this->loadRecipe($filter);
      else
        return $this->recipes[$filter];
    }
    if (is_array($filter))
      return $this->registerRecipe(null, $filter);
    return null;
  }

  public function getStep($filter) : ?CookingStepInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->steps))
        return $this->loadStep($filter);
      else
        return $this->steps[$filter];
    }
    if (is_array($filter))
      return $this->registerStep(null, $filter);
    return null;
  }

  public function getTag($filter) : ?TagInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->tags))
        return $this->loadTag($filter);
      else
        return $this->tags[$filter];
    }
    if (is_array($filter))
      return $this->registerTag(null, $filter);
    if (is_string($filter))
      return $this->loadTagByName($filter);
    return null;
  }

  public function getUnit($filter) : ?UnitInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->units))
        return $this->loadUnit($filter);
      else
        return $this->units[$filter];
    }
    if (is_array($filter))
      return $this->registerUnit(null, $filter);
    return null;
  }

  public function getUser($filter=null) : ?UserInterface {
    if (is_integer($filter)) {
      if (!array_key_exists($filter, $this->users))
        return $this->loadUser($filter);
      else
        return $this->users[$filter];
    }
    if (is_string($filter))
      return $this->loadUsername($filter);
    if (is_array($filter))
      return $this->registerUser(null, $filter);
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
      return true;
    }
    catch (\Exception $e) {
      exit('Error connecting to the database.');
    }
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

  private function loadIngredient(int $id) : ?IngredientInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ingredients', DB_ANY);
    $query->where('recipe_ingredients', 'ingredient_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerIngredient(intval($record['ingredient_id']), $record);
    }
    return $this->registerIngredient($id);
  }

  private function loadPicture(int $id) : ?PictureInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_pictures', DB_ANY);
    $query->where('recipe_pictures', 'picture_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerPicture(intval($record['picture_id']), $record);
    }
    return $this->registerPicture($id);
  }

  private function loadRating(int $id) : ?RatingInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerRating(intval($record['entry_id']), $record);
    }
    return $this->registerRating($id);
  }

  private function loadRecipe(int $id) : ?RecipeInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
    $query->where('recipes', 'recipe_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerRecipe(intval($record['recipe_id']), $record);
    }
    return $this->registerRecipe($id);
  }

  public function loadRecipeIngredients(RecipeInterface &$recipe) : void {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ingredients', DB_ANY);
    $query->where('recipe_ingredients', 'recipe_id', '=', $recipe->getId());
    $result = $this->select($query);
    if ($result) {
      while ($record = $result->fetch_assoc()) {
        $ingredient = $this->getIngredient($record);
        $recipe->addIngredients($ingredient);
      }
    }
  }

  public function loadRecipePictures(RecipeInterface &$recipe) : void {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_pictures', DB_ANY);
    $query->where('recipe_pictures', 'recipe_id', '=', $recipe->getId());
    $result = $this->select($query);
    if ($result) {
      while ($record = $result->fetch_assoc()) {
        $picture = $this->getPicture($record);
        $recipe->addPicture($picture);
      }
    }
  }

  public function loadRecipeRatings(RecipeInterface &$recipe) : void {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'recipe_id', '=', $recipe->getId());
    $result = $this->select($query);
    if ($result) {
      while ($record = $result->fetch_assoc()) {
        $rating = $this->getRating($record);
        $recipe->addRating($rating);
      }
    }
  }

  public function loadRecipeSteps(RecipeInterface &$recipe) : void {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_steps', DB_ANY);
    $query->where('recipe_steps', 'recipe_id', '=', $recipe->getId())
          ->orderBy(['step_no']);
    $result = $this->select($query);
    if ($result) {
      while ($record = $result->fetch_assoc()) {
        $step = $this->getStep($record);
        $recipe->addStep($step);
      }
    }
  }

  public function loadRecipeTags(RecipeInterface &$recipe) : void {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_tags');
    $query->select('tags', DB_ANY)
          ->select('tags', [['tag_id', EAggregationType::atCOUNT, 'count']])
          ->join('tags',
            ['tags', 'tag_id', '=', 'recipe_tags', 'tag_id'],
            ['AND', 'recipe_tags', 'recipe_id', '=', $recipe->getId()])
          ->groupBy('tags', ['tag_id', 'tag_name'])
          ->orderBy2(null, 'count', 'DESC');
    $result = $this->select($query);
    if ($result) {
      while ($record = $result->fetch_assoc()) {
        $tag = $this->getTag($record);
        $recipe->addTag($tag, intval($record['count']));
      }
    }
  }

  private function loadStep(int $id) : ?CookingStepInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_steps', DB_ANY);
    $query->where('recipe_steps', 'step_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerStep(intval($record['step_id']), $record);
    }
    return $this->registerStep($id);
  }

  private function loadTag(int $id) : ?TagInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'tags', DB_ANY);
    $query->where('tags', 'tag_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerTag(intval($record['tag_id']), $record);
    }
    return $this->registerTag($id);
  }

  private function loadTagByName(string $name) : ?TagInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'tags', DB_ANY);
    $query->where('tags', 'tag_name', 'LIKE', $name);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerTag(intval($record['tag_id']), $record);
    }
    return null;
  }

  private function loadUnit(int $id) : ?UnitInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'units', DB_ANY);
    $query->where('units', 'unit_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerUnit(intval($record['unit_id']), $record);
    }
    return $this->registerUnit($id);
  }

  private function loadUser(int $id) : ?UserInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
    $query->where('users', 'user_id', '=', $id);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerUser(intval($record['user_id']), $record);
    }
    return $this->registerUser($id);
  }

  private function loadUsername(string $name) : ?UserInterface {
    $query = new QueryBuilder(EQueryType::qtSELECT, 'users', DB_ANY);
    $query->where('users', 'user_email', 'LIKE', $name)
          ->orWhere('users', 'user_name', 'LIKE', $name);
    $result = $this->select($query);
    if ($record = $result->fetch_assoc()) {
      return $this->registerUser(intval($record['user_id']), $record);
    }
    return null;
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
    $user = $this->loadUsername($uname);
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
    $user = $this->getUser($userid, true);
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
    $user = $this->getUser($email);
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

  private function registerIngredient(?int $id, array $record=null) : ?IngredientInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['ingredient_id']);
    if (array_key_exists($id, $this->ingredients))
      return $this->ingredients[$id];
    if (is_null($record)) {
      $this->ingredients[$id] = null;
      return null;
    }
    $this->ingredients[$id] = new Ingredient($record);
    return $this->ingredients[$id];
  }

  private function registerPicture(?int $id, array $record=null) : ?PictureInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['picture_id']);
    if (array_key_exists($id, $this->pictures))
      return $this->pictures[$id];
    if (is_null($record)) {
      $this->pictures[$id] = null;
      return null;
    }
    $this->pictures[$id] = new Picture($record);
    return $this->pictures[$id];
  }

  private function registerRating(?int $id, array $record=null) : ?RatingInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['entry_id']);
    if (array_key_exists($id, $this->ratings))
      return $this->ratings[$id];
    if (is_null($record)) {
      $this->ratings[$id] = null;
      return null;
    }
    $this->ratings[$id] = new Rating($record);
    return $this->ratings[$id];
  }

  private function registerRecipe(?int $id, array $record=null) : ?RecipeInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['recipe_id']);
    if (array_key_exists($id, $this->recipes))
      return $this->recipes[$id];
    if (is_null($record)) {
      $this->recipes[$id] = null;
      return null;
    }
    $this->recipes[$id] = new Recipe($record);
    return $this->recipes[$id];
  }

  private function registerStep(?int $id, array $record=null) : ?CookingStepInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['step_id']);
    if (array_key_exists($id, $this->steps))
      return $this->steps[$id];
    if (is_null($record)) {
      $this->steps[$id] = null;
      return null;
    }
    $this->steps[$id] = new CookingStep($record);
    return $this->steps[$id];
  }

  private function registerTag(?int $id, array $record=null) : ?TagInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['tag_id']);
    if (array_key_exists($id, $this->tags))
      return $this->tags[$id];
    if (is_null($record)) {
      $this->tags[$id] = null;
      return null;
    }
    $this->tags[$id] = new Tag($record);
    return $this->tags[$id];
  }

  private function registerUnit(?int $id, array $record=null) : ?UnitInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['unit_id']);
    if (array_key_exists($id, $this->units))
      return $this->units[$id];
    if (is_null($record)) {
      $this->units[$id] = null;
      return null;
    }
    $this->units[$id] = new Unit($record);
    return $this->units[$id];
  }

  private function registerUser(?int $id, array $record=null) : ?UserInterface {
    if (is_null($id) && is_array($record))
      $id = intval($record['user_id']);
    if (array_key_exists($id, $this->users))
      return $this->users[$id];
    if (is_null($record)) {
      $this->users[$id] = null;
      return null;
    }
    $this->users[$id] = new User($record);
    return $this->users[$id];
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

  public function select(QueryBuilder &$qbuilder) : ?\mysqli_result {
    $query = $qbuilder->buildQuery();
    $result = $this->database->query($query);
    if (!is_a($result, 'mysqli_result'))
      return null;
    return $result;
  }

  public function selectCountSimple(string $table, string $filterColumn=null, string $filterValue=null) : int {
    $query = new QueryBuilder(EQueryType::qtSELECT, $table);
    $query->select([['*', EAggregationType::atCOUNT, 'count']]);
    if (!is_null($filterColumn))
      $query->where($table, $filterColumn, '=', $filterValue);
    return $this->select($query)->fetch_assoc()['count'];
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
