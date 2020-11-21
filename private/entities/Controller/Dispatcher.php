<?php

namespace Surcouf\Cookbook\Controller;

use Surcouf\Cookbook\Config\EConfigParamKind;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Request\ERequestMethod;
use Surcouf\Cookbook\Response\EOutputMode;
use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\OAuth2Conf;
use Surcouf\Cookbook\User;
use Laravie\Parser\Xml\Reader;
use Laravie\Parser\Xml\Document;

if (!defined('CORE2'))
  exit;

final class Dispatcher {

  private $matched = false,
          $matchedGroups = null,
          $matchedHandler = null,
          $matchedObject = null,
          $matchedPayloadRequirements = false,
          $matchedPattern = null,
          $outputMode = EOutputMode::Default,
          $pageProperties = [],
          $requestMethod = ERequestMethod::Unknown;


  function __construct() {
    if (ISWEB)
      $this->requestMethod = $this->getHttpRequestMethod();
    else
      $this->requestMethod = ERequestMethod::CLI;
  }

  /**
  * Registers a routing information for output to the web browser if the
  * defaults specified in the $params array match the current page request.
  * @param string $routePattern A regex pattern which defines the expected url for this route.
  * @param array $params An associative array with the routing information.
  * @return bool true if route matches the current request.
  */
  public function addRoute(string $routePattern, array $params) : bool {
    global $Controller;

    if (array_key_exists('output', $params))
      $this->outputMode = $params['output'];
    else if (strpos($_SERVER['REQUEST_URI'], '/ajax/') === 0 || strpos($_SERVER['REQUEST_URI'], '/api/') === 0)
      $this->outputMode = EOutputMode::JSON;
    else
      $this->outputMode = EOutputMode::Default;

    if (
         $this->evaluateRouteMaintenance($params)
      && $this->evaluateRouteMethod($params)
      && $this->evaluateRoutePattern($routePattern)
      && $this->evaluateRouteUser($params)
    ) {
      $this->matched = true;
      $this->matchedGroups = $this->routePatternMatches;
      $this->matchedPattern = $routePattern;
      $this->matchedHandler = $params['class'];
      $this->matchedPayloadRequirements = $this->evaluateRoutePayload($params);

      if (array_key_exists('properties', $params))
        $this->pageProperties = $params['properties'];

      if (array_key_exists('createObject', $params)) {
        $method = $params['createObject']['method'];
        $this->matchedObject = $Controller->OM()->$method(intval($this->getFromMatches($params['createObject']['idkey'])));
      }

      return true;
    }

    return false;
  }

  /**
   * Generates the output data for the called Url.
   */
  public function dispatchRoute() : void {
    global $Controller, $OUT, $twig;

    if (!$this->matched)
      $this->exitError(70, null, null, null, $Controller->getLink('private:home'));

    if ($this->outputMode == EOutputMode::Default)
      require_once DIR_BACKEND  .'/web.php';

    $response = [];
    $result = $this->matchedHandler::createOutput($response);

    if (!$result)
      $this->exitError(null, null, $response);

    if ($this->outputMode == EOutputMode::JSON) {
      $this->exitJson(!is_null($response) ? $response : $Controller->Config()->getResponseArray(10));
    }

    $this->tearDown();
    header('X-Frame-Options: DENY');
    echo $twig->render('body.html.twig', $OUT);
    exit;

  }

  /**
   * Checks if request is available for active maintenance mode.
   * @param array $params An associative array with the routing information.
   * @return bool true if matching method.
   */
  private function evaluateRouteMaintenance(array $params) : bool {
    if ((array_key_exists('ignoreMaintenance', $params) && $params['ignoreMaintenance'] === true && MAINTENANCE == true ) ||
        MAINTENANCE === false)
      return true;
    return false;
  }

  /**
   * Checks if the HTTP REQUEST_METHOD matches the method required for the
   * function.
   * @param array $params An associative array with the routing information.
   * @return bool true if matching method.
   */
  private function evaluateRouteMethod(array $params) : bool {
    if ((!array_key_exists('method', $params) && $this->requestMethod == ERequestMethod::HTTP_GET) ||
        (array_key_exists('method', $params) && $params['method'] == $this->requestMethod))
      return true;
    return false;
  }

  /**
   * Checks if the requested page Url matches the route pattern.
   * @param string $routePattern A regex pattern which defines the expected url for this route.
   * @return bool true if matching pattern.
   */
  private function evaluateRoutePattern(string $routePattern) : bool {
    $pattern = str_replace('/', '\\/', $routePattern);
    $this->routePatternMatches = array();
    if (preg_match('/^'.$pattern.'$/', $_SERVER['REQUEST_URI'], $this->routePatternMatches)) {
      return true;
    }
    return false;
  }

  /**
   * If the page is called via HTTP POST, the route may require certain POST
   * information (payload). This function checks if this information is
   * available in $_POST.
   * @param array $params An associative array with the routing information.
   * @return bool true if matching method.
   */
  private function evaluateRoutePayload(array $params) : bool {
    if ($this->requestMethod != ERequestMethod::HTTP_POST)
      return true;
    if (array_key_exists('requiresPayload', $params)) {
      $payload = $params['requiresPayload'];
      if (is_string($payload))
        return array_key_exists($payload, $_POST);
      for($i=0; $i<count($payload); $i++) {
        if (!array_key_exists($payload[$i], $_POST))
          return false;
      }
    }
    return true;
  }

  /**
   * Checks if the requirements for user authentication are fullfilled.
   * @param array $params An associative array with the routing information.
   * @return bool true if matching method.
   */
  private function evaluateRouteUser(array $params) : bool {
    global $Controller;
    if (!$Controller->isAuthenticated()) {
      // if no user logged in, route must define 'requiresUser' with false
      // and must not define 'requiresAdmin'
      if (!array_key_exists('requiresUser', $params) ||
          $params['requiresUser'] !== false ||
          array_key_exists('requiresAdmin', $params))
        $this->forwardTo($Controller->getLink('private:login'));
    } else {
      // if route requires admin check if user is admin
      if (array_key_exists('requiresAdmin', $params) &&
          $params['requiresAdmin'] !== false &&
          !$Controller->User()->isAdmin())
        $this->exitError(120);
    }
    return true;
  }

  /**
  * This function forces the termination of the processing and an output depending
  * on the request type (CLI, HTTP, HTTP ajax) and DEBUG setting.
  * @param  int $code         The internal response code.
  * @param  string $message   An error message.
  * @param  array $response   A preconfigured response array.
  * @param  int $httpCode     HTTP status code to be displayed to the user (only if not CLI, not ajax and not DEBUG).
  * @throws \Exception        If DEBUG is active or the call was made via CLI.
  */
  public function exitError(int $code = null,
                            string $message = null,
                            array $response = null,
                            int $httpCode = null,
                            string $forwardTo = null) : void {
    global $Controller;
    $this->tearDown();
    if (ISWEB) {
      if (!is_null($code))
        $response = $Controller->Config()->getResponseArray($code);
      if ($this->outputMode == EOutputMode::JSON && !is_null($response)) {
        $this->exitJson($response);
      }
      if (!is_null($forwardTo))
        $this->forwardTo($forwardTo);
      if (DEBUG === true) {
        throw new \Exception('ERROR '.$code.': '.$message);
      }
      if (!is_null($httpCode))
        http_response_code($httpCode);
      exit;
    }
    if (!is_null($response))
      throw new \Exception('ERROR '.$response['Result']['Error']['Code'].': '.$response['Result']['Error']['Message']);
    throw new \Exception('ERROR '.$code.': '.$message);
  }

  /**
  * This function sends the passed array to the client (HTTP = JSON, CLI = var_dump!)
  * @param  Array $response   A preconfigured response array.
  */
  function exitJson(Array $response) : void {
    $this->tearDown();
    if (ISWEB) {
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
    }
    var_dump($response);
    exit;
  }

  public function finishOAuthLogin(array &$response) : bool {
    global $Controller;
    $provider = $Controller->getOAuthProvider();
    session_start();

    if (!array_key_exists('state', $_GET) || !array_key_exists('oauth2state', $_SESSION) || $_SESSION['oauth2state'] != $_GET['state']) {
      unset($_SESSION['oauth2state']);
      session_destroy();
      return false;
    }
    try {
      $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
      ]);
      $isUserCreated = false;
      if ($Controller->loginWithOAuth($accessToken, $isUserCreated)) {
        $this->forwardTo($Controller->getLink('private:home'));
      }
      return false;
    } catch(\Exception $e) {
      return false;
    }
  }

  /**
  * This function sends a forwarding header to the client, or a corresponding
  * error message to the CLI.
  * @param string $newUrl The Url to which is forwarded.
  */
  public function forwardTo(string $newUrl) : void {
    global $Controller;
    if (ISWEB) {
      if ($this->outputMode == EOutputMode::JSON) {
        $response = $Controller->Config()->getResponseArray(12);
        $response['Result']['Error']['ForwardTo'] = $newUrl;
        $this->exitJson($response);
      }
      $this->tearDown();
      header('Location:'.$newUrl);
      exit;
    }
    $this->exitError(12, 'The called function tries to redirect you.');
  }

  public function getFromMatches(string $key) : ?string {
    return array_key_exists($key, $this->matchedGroups) ? $this->matchedGroups[$key] : null;
  }

  public function getFromPayload(string $key) : ?string {
    return array_key_exists($key, $_POST) ? $_POST[$key] : null;
  }

  /**
  * This function returns the ERequestMethod enumeration value for the current
  * HTTP request method.
  * @return string with cosnt values from Surcouf\Cookbook\Request\ERequestMethod
  */
  private function getHttpRequestMethod() : ?string {
    switch($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return ERequestMethod::HTTP_GET;
      case 'POST':
        return ERequestMethod::HTTP_POST;
      case 'PUT':
        return ERequestMethod::HTTP_PUT;
      case 'HEAD':
        return ERequestMethod::HTTP_HEAD;
      case 'DELETE':
        return ERequestMethod::HTTP_DELETE;
      case 'PATCH':
        return ERequestMethod::HTTP_PATCH;
      case 'OPTIONS':
        return ERequestMethod::HTTP_OPTIONS;
    }
    return ERequestMethod::Unknown;
  }

  public function getMatches() : array {
    return $this->matchedGroups;
  }

  public function getObject() : ?object {
    return $this->matchedObject;
  }

  public function getPayload() : array {
    return $_POST;
  }

  public function getPattern() : string {
    return $this->matchedPattern;
  }

  public function moved(string $url) : void {
    header('Location: '.$url, true, 301);
    exit();
  }

  public function notFound() : void {
    header('HTTP/1.0 404 Not Found', true, 404);
    exit;
  }

  public function notImplemented() : void {
    header('HTTP/1.0 501 Not Implemented', true, 501);
    exit;
  }

  public function queryOAuthUserData() : bool {
    global $Controller;
    $provider = $Controller->getOAuthProvider();
    try {
      $request = $provider->getAuthenticatedRequest(
        'GET',
        $Controller->getLink('admin:oauth:user'),
        $Controller->User()->getSession()->getToken()
      );
      $client = new \GuzzleHttp\Client();
      $response = $client->sendRequest($request);
      if ($response->getStatusCode() == 200 && $response->getHeader('content-type')[0]) {
        $xml = (new Reader(new Document()))->extract((string)$response->getBody());
        $meta = $xml->parse([
          'statuscode' => ['uses' => 'meta.statuscode']
        ]);
        if ($meta['statuscode'] == '100') {
          $userdata = $xml->parse([
            'email' => ['uses' => 'data.email'],
            'fullname' => ['uses' => 'data.display-name']
          ]);
          $Controller->User()->setMail($userdata['email']);
          $Controller->User()->setName($userdata['fullname']);
          $Controller->User()->setRegistrationCompleted();
          return true;
        }
      }
    } catch (\Exception $e) {
      // Don't care for exceptions right now
    }
    return false;
  }

  public function startOAuthLogin() : void {
    global $Controller;
    $provider = $Controller->getOAuthProvider();
    $authorizationUrl = $provider->getAuthorizationUrl();
    session_start();
    $_SESSION['oauth2state'] = $provider->getState();
    $this->forwardTo($authorizationUrl);
  }

  private function tearDown() : void {
    global $Controller;
    $Controller->tearDown();
  }
















/*
  private function dispatch() : void {

    if (MAINTENANCE && !$this->fnIgnoresMaintenanceMode)
      $this->exitError(100, null, null, null, '/maintenance');

    if (!$this->matched)
      $this->exitError(70, null, null, null, '/');

    if ($this->fnRequiresAuthentication && !$this->controller->isAuthenticated())
      $this->exitError(111, null, null, null, '/login');

    if (!$this->fnSelfRegistration &&
        $this->controller->isAuthenticated() &&
        !$this->controller->User()->hasRegistrationCompleted() &&
        !$this->controller->User()->getSession()->isExpired())
      $this->forwardTo($this->controller->getLink('private:self-register'));

    $response = null;

    if (is_callable($this->matchedHandler))
      $response = call_user_func($this->matchedHandler);
    else {
      if (function_exists($this->matchedHandler))
        $response = call_user_func($this->matchedHandler);
      else
        $this->exitError(71, null, null, null, '/');
    }

    if ($this->outputMode == EOutputMode::JSON) {
      $this->exitJson(!is_null($response) ? $response : $this->controller->Config()->getResponseArray(10));
    }
    global $OUT, $start, $twig;
    $this->controller->tearDown();
    header('X-Frame-Options: DENY');
    $OUT['time'] = microtime(true) - $start;
    echo $twig->render('body.html.twig', $OUT);
    exit;

  }

  private function evaluateDispatch() : bool {

    if (MAINTENANCE && !$this->fnIgnoresMaintenanceMode)
      $this->forwardTo($this->controller->getLink('maintenance'));

    if ($this->fnRequiresAuthentication && !$this->controller->isAuthenticated())
      $this->forwardTo($this->controller->getLink('private:login'));

    if (!is_null($this->fnRequiredPayload)) {
      for($i=0; $i<count($this->fnRequiredPayload); $i++) {
        if (!array_key_exists($this->fnRequiredPayload[$i], $_POST))
          $this->exitJson($this->controller->Config()->getResponseArray(80));
      }
    }

    return true;

  }*/

  /**
  * This function is called to register a function call for a URL. In the array $params various switches can be set, at least "pattern" is required.
  * @param  string $method    The request method for which this function is allowed (according to Surcouf\Cookbook\Request\ERequestMethod)
  * @param  array $params     An associative array to configure the function call.
  *//*
  function on(string $method, array $params) : void {
    if ($this->requestMethod == $method) {
      $pattern = str_replace('/', '\\/', $params['pattern']);
      $m = array();
      if (preg_match('/^'.$pattern.'$/', $_SERVER['REQUEST_URI'], $m)) {
        $this->matchedPattern = $pattern;
        $this->matchedHandler = $params['fn'];
        $this->matched = true;
        $this->matchedGroups = $m;
        if (!array_key_exists('requiresAuthentication', $params))
          $this->fnRequiresAuthentication = true;
        else
          $this->fnRequiresAuthentication = ConverterHelper::to_bool($params['requiresAuthentication']);
        if (array_key_exists('ignoreMaintenance', $params)) {
          $this->fnIgnoresMaintenanceMode = ConverterHelper::to_bool($params['ignoreMaintenance']);
        }
        if (array_key_exists('outputMode', $params)) {
          $this->outputMode = $params['outputMode'];
        } else if (strpos($_SERVER['REQUEST_URI'], '/ajax/') === 0) {
          $this->outputMode = EOutputMode::JSON;
        }
        if (array_key_exists('requiredPayload', $params)) {
          $this->fnRequiredPayload = $params['requiredPayload'];
        }
        if (array_key_exists('isSelfregistration', $params)) {
          $this->fnSelfRegistration = $params['isSelfregistration'];
        }

        if (!$this->evaluateDispatch()) {
          $this->matchedPattern = null;
          $this->matchedHandler = null;
          $this->matched = false;
          $this->matchedGroups = false;
          $this->fnRequiresAuthentication = true;
          $this->fnIgnoresMaintenanceMode = false;
        }
        else
          $this->dispatch();
      }
    }
  }*/

  /**
  * Short form for dispatcher::on(ERequestMethod::HTTP_POST, $params).
  * @param  array $params     An associative array to configure the function call.
  *//*
  function post(array $params) : void {
    $this->on(ERequestMethod::HTTP_POST, $params);
  }*/

  /**
  * Short form for dispatcher::on(ERequestMethod::HTTP_PUT, $params).
  * @param  array $params     An associative array to configure the function call.
  *//*
  function put(array $params) : void {
    $this->on(ERequestMethod::HTTP_PUT, $params);
  }*

  public function getMatchInt(string $key, int $fallback = -1) : ?int {
    if (array_key_exists($key, $this->matchedGroups))
      return intval($this->matchedGroups[$key]);
    return $fallback;
  }

  public function getMatchString(string $key, string $fallback = '') : ?string {
    if (array_key_exists($key, $this->matchedGroups))
      return $this->matchedGroups[$key];
    return $fallback;
  }*/

}
