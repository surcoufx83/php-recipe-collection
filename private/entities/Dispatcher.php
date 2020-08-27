<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config\EConfigParamKind;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Request\ERequestMethod;
use Surcouf\Cookbook\Response\EOutputMode;
use Surcouf\Cookbook\Controller;
use Surcouf\Cookbook\User;

if (!defined('CORE2'))
  exit;

class Dispatcher {

  private $controller;
  private $requestMethod = ERequestMethod::Unknown;

  private $matched = false, $matchedGroups = array(), $matchedHandler, $matchedPattern;

  private $fnIgnoresMaintenanceMode = false;
  private $fnOutputMethod = EOutputMode::Default;
  private $fnRequiredPermission = array();
  private $fnRequiredPayload = null;
  private $fnRequiresAuthentication = false;

  function __construct(Controller &$controller) {
    $this->controller = $controller;
    if (ISWEB)
      $this->requestMethod = $this->getHttpRequestMethod();
    else
      $this->requestMethod = ERequestMethod::CLI;
  }

  private function dispatch() : void {

    if (MAINTENANCE && !$this->fnIgnoresMaintenanceMode)
      $this->exitError(100, null, null, null, '/maintenance');

    if (!$this->matched)
      $this->exitError(70, null, null, null, '/');

    if ($this->fnRequiresAuthentication && !$this->controller->isAuthenticated())
      $this->exitError(111, null, null, null, '/login');

    $response = null;

    if (is_callable($this->matchedHandler))
      $response = call_user_func($this->matchedHandler);
    else {
      if (function_exists($this->matchedHandler))
        $response = call_user_func($this->matchedHandler);
      else
        $this->exitError(71, null, null, null, '/');
    }

    if ($this->fnOutputMethod == EOutputMode::JSON) {
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
      $this->forward($this->controller->getLink('maintenance'));

    if ($this->fnRequiresAuthentication && !$this->controller->isAuthenticated())
      $this->forward($this->controller->getLink('private:login'));

    if (!is_null($this->fnRequiredPayload)) {
      for($i=0; $i<count($this->fnRequiredPayload); $i++) {
        if (!array_key_exists($this->fnRequiredPayload[$i], $_POST))
          $this->exitJson($this->controller->Config()->getResponseArray(80));
      }
    }

    return true;

  }

  /**
  * This function forces the termination of the processing and an output depending on the request type (CLI, HTTP, HTTP ajax) and DEBUG setting.
  * @param  int $code         The internal response code.
  * @param  string $message   An error message.
  * @param  Array $response   A preconfigured response array.
  * @param  int $httpCode     HTTP status code to be displayed to the user (only if not CLI, not ajax and not DEBUG).
  * @throws \Exception        If DEBUG is active or the call was made via CLI.
  */
  function exitError(int $code = null, string $message = null, Array $response = null, int $httpCode = null, string $forwardTo = null) : void {
    $this->controller->tearDown();
    if (ISWEB) {
      if (!is_null($code))
        $response = $this->controller->Config()->getResponseArray($code);
      if ($this->fnOutputMethod == EOutputMode::JSON && !is_null($response)) {
        $this->exitJson($response);
      }
      if (!is_null($forwardTo))
        $this->forward($forwardTo);
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
    exit;
  }

  /**
  * This function sends the passed array to the client (HTTP = JSON, CLI = var_dump!)
  * @param  Array $response   A preconfigured response array.
  */
  function exitJson(Array $response) : void {
    $this->controller->tearDown();
    if (ISWEB) {
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
    }
    var_dump($response);
    exit;
  }

  /**
  * This function sends a forwarding header to the client, or a corresponding error message to the CLI.
  * @param  string $moveTo   The URL that is referred to.
  */
  function forward(string $moveTo) : void {
    $this->controller->tearDown();
    if (ISWEB) {
      if ($this->fnOutputMethod == EOutputMode::JSON) {
        $response = $this->controller->Config()->getResponseArray(12);
        $response['Result']['Error']['ForwardTo'] = $moveTo;
        $this->exitJson($response);
      }
      header('Location:'.$moveTo);
      exit;
    }
    $this->exitError(12, 'The called function tries to redirect you.');
  }

  /**
  * Short form for dispatcher::on(ERequestMethod::HTTP_GET, $params).
  * @param  array $params     An associative array to configure the function call.
  */
  function get(array $params) : void {
    $this->on(ERequestMethod::HTTP_GET, $params);
  }

  /**
  * This function returns the ERequestMethod enumeration value for the current HTTP request method.
  * @return Surcouf\Cookbook\Request\ERequestMethod
  */
  private function getHttpRequestMethod() {
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
    $this->exitError(11);
  }

  /**
  * This function is called to register a function call for a URL. In the array $params various switches can be set, at least "pattern" is required.
  * @param  string $method    The request method for which this function is allowed (according to Surcouf\Cookbook\Request\ERequestMethod)
  * @param  array $params     An associative array to configure the function call.
  */
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
          $this->fnOutputMethod = $params['outputMode'];
        } else if (strpos($_SERVER['REQUEST_URI'], '/ajax/') === 0) {
          $this->fnOutputMethod = EOutputMode::JSON;
        }
        if (array_key_exists('requiredPayload', $params)) {
          $this->fnRequiredPayload = $params['requiredPayload'];
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
  }

  /**
  * Short form for dispatcher::on(ERequestMethod::HTTP_POST, $params).
  * @param  array $params     An associative array to configure the function call.
  */
  function post(array $params) : void {
    $this->on(ERequestMethod::HTTP_POST, $params);
  }

  /**
  * Short form for dispatcher::on(ERequestMethod::HTTP_PUT, $params).
  * @param  array $params     An associative array to configure the function call.
  */
  function put(array $params) : void {
    $this->on(ERequestMethod::HTTP_PUT, $params);
  }

  public function getMatchInt(string $key, int $fallback = -1) : ?int {
    if (array_key_exists($key, $this->matchedGroups))
      return intval($this->matchedGroups[$key]);
    return $fallback;
  }

  public function getMatchString(string $key, string $fallback = '') : ?string {
    if (array_key_exists($key, $this->matchedGroups))
      return $this->matchedGroups[$key];
    return $fallback;
  }

  public function getMatches() : array {
    return $this->matchedGroups;
  }

  public function getPattern() : string {
    return $this->matchedPattern;
  }

  public function getPayload() : array {
    return $_POST;
  }

}
